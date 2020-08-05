<?php

namespace App\Services;

use App\Events\Media\MediaHasBeenAdded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Finder\Finder;

class MediaUploadService
{
    /**
     * @var MediaMetadataService
     */
    protected $mediaMetadataService;

    /**
     * @param MediaMetadataService $mediaMetadataService
     */
    public function __construct(MediaMetadataService $mediaMetadataService)
    {
        $this->mediaMetadataService = $mediaMetadataService;
    }

    /**
     * @param Model  $model
     * @param string $path
     * @param string $collection
     *
     * @return void
     */
    public function importByPath(Model $model, string $path, string $collection): void
    {
        $files = $this->getFilesInPath($path);

        foreach ($files as $file) {
            $filePath = $file->getRealPath();
            $fileName = $file->getFilename();

            throw_if(
                !$this->mediaMetadataService->isProbable($filePath),
                ValidationException::class,
                "Unable to probe file {$fileName}"
            );

            // Start importing file
            $media = $model->addMedia($filePath)
                  ->usingName(
                      $this->convertFilename($fileName)
                  )
                  ->toMediaCollection($collection);

            // Set metadata
            $this->mediaMetadataService->setMetadata($media);

            event(new MediaHasBeenAdded($media));
        }
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
            ->filter($filter)
            ->sortByName();
    }

    /**
     * @return array
     */
    protected function supportedFileNames(): array
    {
        $extensions = collect(
            config('vod.extensions')
        );

        return $extensions->map(function ($item) {
            return "*.{$item}";
        })->toArray();
    }

    /**
     * @return array
     */
    protected function supportedMimeTypes(): array
    {
        return config('vod.mimetypes');
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
