<?php

namespace App\Services;

use App\Models\Media;

class ThumbnailService
{
    public function __construct(
        protected FFMpegService $ffmpegService,
        protected ConversionService $conversionService,
    ) {
    }

    public function create(Media $media): void
    {
        $path = $this->conversionService->temporaryDirectory()->path(
            config('api.conversions.thumbnail.path')
        );

        $video = $this->ffmpegService->openFile(
            $media->getPath()
        );

        $duration = $media->getCustomProperty('metadata.duration', 60);
        $timeCode = $media->getCustomProperty('thumbnail', $duration / 2);

        $this->ffmpegService->createThumbnail(
            $video,
            $path,
            $timeCode,
            config('api.conversions.thumbnail.filter')
        );

        ImageService::optimize($path);

        $this->conversionService->move(
            $media,
            $path,
            config('api.conversions.thumbnail.path')
        );

        $media->markAsConversionGenerated('thumbnail');
    }
}
