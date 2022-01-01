<?php

namespace App\Actions\Video;

use App\Events\Media\MediaHasBeenAdded;
use App\Events\Video\VideoHasBeenAdded;
use App\Models\Video;
use Symfony\Component\Finder\SplFileInfo;

class ImportMediaToLibrary
{
    public function __invoke(
        Video $video,
        SplFileInfo $file,
        string $collection = null,
        array $properties = []
    ): void {
        $path = $file->getRealPath();
        $extension = $file->getExtension();

        $media = $video
            ->addMedia($path)
            ->withCustomProperties($properties)
            ->toMediaCollection($collection);

        // Force WebVTT
        if ('vtt' === $extension) {
            $media->mime_type = 'text/vtt';
            $media->saveOrFail();
        }

        // Process media
        MediaHasBeenAdded::dispatch($media);

        // Process video
        VideoHasBeenAdded::dispatch($video);
    }
}
