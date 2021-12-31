<?php

namespace App\Actions\Media;

use App\Models\Media;
use App\Services\FFMpegService;
use App\Services\ImageService;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\Coordinate\TimeCode;

class CreateMediaThumbnail
{
    public function __invoke(Media $media, string $path): void
    {
        $conversion = match ($media->type) {
            'video' => $this->videoFrame($media, $path)
        };

        if ($conversion) {
            $this->optimize($path);
        }
    }

    protected function videoFrame(Media $media, string $path): void
    {
        $duration = $media->getCustomProperty('duration') ?? 10;
        $timeCode = $media->getCustomProperty('thumbnail') ?? round($duration / 2, 3);

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
