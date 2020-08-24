<?php

namespace App\Services;

use App\Events\Video\MediaHasBeenAdded;
use App\Models\Video;
use Symfony\Component\Finder\Finder;

class VideoTrackImportService
{
    /**
     * @param Video  $video
     * @param string $path
     * @param string $type
     *
     * @return void
     */
    public function import(
        Video $video,
        string $path,
        string $type
    ): void {
        $files = $this->getFilesInPath($path);

        foreach ($files as $file) {
            $filePath = $file->getRealPath();
            $fileExtension = $file->getExtension();

            // Import track file to media-library
            $media = $video
                ->addMedia($filePath)
                ->withCustomProperties([
                    'type' => $type,
                ])
                ->toMediaCollection('tracks');

            // Force WebVTT
            if ('vtt' === $fileExtension) {
                $media->mime_type = 'text/vtt';
                $media->save();
            }

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
    protected function supportedMimeTypes(): array
    {
        return config('vod.tracks.mimetypes');
    }
}
