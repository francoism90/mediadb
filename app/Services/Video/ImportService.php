<?php

namespace App\Services\Video;

use App\Events\Video\MediaHasBeenAdded;
use App\Models\Collection;
use App\Models\User;
use App\Services\Collection\SyncService as CollectionSyncService;
use App\Services\Media\MetadataService;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Finder\Finder;
use Throwable;

class ImportService
{
    /**
     * @var CollectionSyncService
     */
    protected $collectionService;

    /**
     * @var MetadataService
     */
    protected $metadataService;

    public function __construct(
        CollectionSyncService $collectionSyncService,
        MetadataService $metadataService
    ) {
        $this->collectionService = $collectionSyncService;
        $this->metadataService = $metadataService;
    }

    /**
     * @param User   $user
     * @param string $collection
     * @param string $path
     * @param string $type
     *
     * @return void
     */
    public function import(
        User $user,
        string $collection,
        string $path,
        string $type = null
    ): void {
        $collectionModel = $this->createCollection($user, $collection);

        $files = $this->getFilesInPath($path);

        foreach ($files as $file) {
            try {
                $filePath = $file->getRealPath();
                $fileName = $file->getFilenameWithoutExtension();

                throw_if(
                    !$this->metadataService->isProbable($filePath),
                    ValidationException::class,
                    "Unable to probe path: {$filePath}"
                );

                $video = $user->videos()->create([
                    'name' => $this->convertFilename($fileName),
                    'type' => $type,
                ]);

                $media = $video
                    ->addMedia($filePath)
                    ->toMediaCollection('clip');

                $this->metadataService->setAttributes($media);

                $collectionModel->videos()->attach($video);

                event(new MediaHasBeenAdded($video, $media));
            } catch (Throwable $e) {
                report($e);
            }
        }
    }

    /**
     * @param User   $user
     * @param string $collection
     *
     * @return Collection
     */
    protected function createCollection(User $user, string $collection): Collection
    {
        $collections = $this->collectionService->create(
            $user,
            collect([
                ['name' => $collection, 'type' => 'title'],
            ])
        );

        return $collections->first();
    }

    /**
     * @param string $path
     *
     * @return Finder
     */
    protected function getFilesInPath(string $path): Finder
    {
        // Ignore unreadable files
        $filter = function (\SplFileInfo $file) {
            if (!$file->isReadable() || !$file->isWritable()) {
                return false;
            }

            $mime = mime_content_type($file->getRealPath());

            return in_array($mime, $this->supportedMimeTypes());
        };

        return (new Finder())
            ->files()
            ->in($path)
            ->depth('== 0')
            ->name($this->supportedFileNames())
            ->size($this->supportedFileSize())
            ->filter($filter)
            ->sortByName();
    }

    /**
     * @return array
     */
    protected function supportedFileNames(): array
    {
        $extensions = collect(
            config('vod.video.extensions')
        );

        return $extensions->map(function ($item) {
            return "*.{$item}";
        })->toArray();
    }

    /**
     * @return array
     */
    protected function supportedFileSize(): array
    {
        return config('vod.video.size');
    }

    /**
     * @return array
     */
    protected function supportedMimeTypes(): array
    {
        return config('vod.video.mimetypes');
    }

    /**
     * @param string $value
     *
     * @return string
     */
    protected function convertFilename(string $value): string
    {
        $str = str_replace(['.', ',', '_', '-'], ' ', $value);
        $str = preg_replace('/\s+/', ' ', trim($str));

        return Str::title($str);
    }
}
