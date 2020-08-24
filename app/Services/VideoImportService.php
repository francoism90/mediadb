<?php

namespace App\Services;

use App\Events\Video\MediaHasBeenAdded;
use App\Models\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Finder\Finder;

class VideoImportService
{
    /**
     * @var VideoMetadataService
     */
    protected $videoMetadataService;

    /**
     * @param VideoMetadataService $videoMetadataService
     */
    public function __construct(VideoMetadataService $videoMetadataService)
    {
        $this->videoMetadataService = $videoMetadataService;
    }

    /**
     * @param Collection $collection
     * @param string     $path
     * @param string     $type
     *
     * @return void
     */
    public function import(
        Collection $collection,
        string $path,
        string $type = null
    ): void {
        $files = $this->getFilesInPath($path);

        foreach ($files as $file) {
            $filePath = $file->getRealPath();
            $fileName = $file->getFilenameWithoutExtension();

            throw_if(
                !$this->videoMetadataService->isProbable($filePath),
                ValidationException::class,
                "Unable to import file: {$fileName}"
            );

            // Create video model
            $model = $collection->model;

            $video = $model->videos()->create([
                'name' => $this->convertFilename($fileName),
                'type' => $type,
            ]);

            // Import video file to media-library
            $media = $video
                ->addMedia($filePath)
                ->toMediaCollection('clip');

            // Attach video to model
            $collection->videos()->attach($video);

            // Set video metadata
            $this->videoMetadataService->setAttributes($media);

            event(new MediaHasBeenAdded($video, $media));
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
