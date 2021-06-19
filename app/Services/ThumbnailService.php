<?php

namespace App\Services;

use App\Models\Media;

class ThumbnailService
{
    public function __construct(
        protected FFMpegService $ffmpegService,
        protected ConversionService $conversionService,
        protected ImageService $imageService
    ) {
    }

    public function create(Media $media): void
    {
        $path = $this->conversionService->temporaryDirectory()->path(
            config('media.thumbnail_name')
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
            config('media.thumbnail_filter')
        );

        $this->imageService->optimize($path);

        $this->conversionService->import(
            $media,
            $path,
            config('media.thumbnail_name')
        );

        $media->markAsConversionGenerated('thumbnail');
    }
}
