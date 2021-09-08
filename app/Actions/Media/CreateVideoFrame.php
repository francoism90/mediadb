<?php

namespace App\Actions\Media;

use App\Models\Media;
use App\Services\FFMpegService;
use App\Services\ImageService;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\Filters\Video\CustomFilter;
use Spatie\QueueableAction\QueueableAction;

class CreateVideoFrame
{
    use QueueableAction;

    public function __construct(
        protected FFMpegService $ffmpegService,
    ) {
    }

    public function execute(Media $media, string $path): void
    {
        $this->create($media, $path);

        $this->optimize($path);
    }

    private function create(Media $media, string $path): void
    {
        $duration = $media->getCustomProperty('metadata.duration', 60);
        $timeCode = $media->getCustomProperty('thumbnail', $duration / 2);

        $file = $this->ffmpegService->open(
            $media->getPath()
        );

        $frame = $file->frame(
            TimeCode::fromSeconds($timeCode)
        );

        $frame->addFilter(
            new CustomFilter(config('api.conversions.thumbnail.filter'))
        );

        $frame->save($path);
    }

    private function optimize(string $path): void
    {
        app(ImageService::class)::optimize($path);
    }
}
