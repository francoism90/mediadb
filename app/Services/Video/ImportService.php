<?php

namespace App\Services\Video;

use App\Events\Video\MediaHasBeenAdded;
use App\Models\Video;
use Symfony\Component\Finder\Finder;
use Throwable;

class ImportService
{
    public function import(
        Video $video,
        string $path,
        string $collection,
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
                    ->toMediaCollection($collection);

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
            config('vod.import.extensions')
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
        return config('vod.import.size');
    }

    /**
     * @return array
     */
    protected function supportedMimeTypes(): array
    {
        return config('vod.import.mimetypes');
    }
}
