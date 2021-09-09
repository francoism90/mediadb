<?php

namespace App\Actions\Media;

use App\Models\Media;
use App\Services\FFMpegService;
use App\Services\ImageService;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\Filters\Frame\CustomFrameFilter;

class CreateVideoFrame
{
    public function __construct(
        protected FFMpegService $ffmpegService,
    ) {
    }

    public function execute(Media $media, string $path): void
    {
        $this->create($media, $path);

        $this->optimize($path);
    }

    protected function create(Media $media, string $path): void
    {
        $duration = $media->getCustomProperty('duration', 10);
        $timeCode = $media->getCustomProperty('thumbnail', $duration / 2);

        $file = $this->ffmpegService->open(
            $media->getPath()
        );

        $frame = $file->frame(
            TimeCode::fromSeconds($timeCode)
        );

        $frame->addFilter(
            new CustomFrameFilter(
                config('api.video.conversions.thumbnail.filter')
            )
        );

        $frame->save($path);
    }

    protected function optimize(string $path): void
    {
        app(ImageService::class)::optimize($path);
    }
}
