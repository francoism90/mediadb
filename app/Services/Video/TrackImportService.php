<?php

namespace App\Services\Video;

use App\Events\Video\MediaHasBeenAdded;
use App\Models\Video;
use Symfony\Component\Finder\Finder;
use Throwable;

class TrackImportService
{
    /**
     * @param Video  $video
     * @param string $path
     * @param string $properties
     *
     * @return void
     */
    public function import(
        Video $video,
        string $path,
        array $properties = []
    ): void {
        $files = $this->getFilesInPath($path);

        foreach ($files as $file) {
            try {
                $filePath = $file->getRealPath();
                $fileExtension = $file->getExtension();

                $media = $video
                    ->addMedia($filePath)
                    ->withCustomProperties($properties)
                    ->toMediaCollection('tracks');

                // Force WebVTT
                if ('vtt' === $fileExtension) {
                    $media->mime_type = 'text/vtt';
                    $media->save();
                }

                event(new MediaHasBeenAdded($video, $media));
            } catch (Throwable $e) {
                report($e);
            }
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
            config('vod.tracks.extensions')
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
        return config('vod.tracks.size');
    }

    /**
     * @return array
     */
    public function supportedMimeTypes(): array
    {
        return config('vod.tracks.mimetypes');
    }

    /**
     * @return array
     */
    public function supportedTypes(): array
    {
        return config('vod.tracks.types');
    }

    /**
     * @return array
     */
    public function supportedLanguages(): array
    {
        return array_keys(config('vod.tracks.languages'));
    }
}
