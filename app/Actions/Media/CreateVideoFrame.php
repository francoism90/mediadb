<?php

namespace App\Actions\Media;

use App\Models\Media;
use App\Services\FFMpegService;
use App\Services\ImageService;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\Coordinate\TimeCode;

class CreateVideoFrame
{
    public function __invoke(Media $media, string $path): void
    {
        $this->create($media, $path);
        $this->optimize($path);
    }

    protected function create(Media $media, string $path): void
    {
        $duration = $media->getCustomProperty('duration') ?? 10;
        $timeCode = $media->getCustomProperty('thumbnail') ?? $duration / 2;

        $video = app(FFMpegService::class)->open(
            $media->getPath()
        );

        $video
            ->filters()
            ->resize(new Dimension(320, 240))
            ->synchronize();

        $video
            ->frame(TimeCode::fromSeconds($timeCode))
            ->save($path);
    }

    protected function optimize(string $path): void
    {
        app(ImageService::class)::optimize($path);
    }
}
