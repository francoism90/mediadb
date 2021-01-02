<?php

namespace App\Services;

use App\Models\Media;

class MediaThumbnailService
{
    /**
     * @var FFMpegService
     */
    protected FFMpegService $ffmpegService;

    /**
     * @var MediaConversionService
     */
    protected MediaConversionService $conversionService;

    /**
     * @var ImageService
     */
    protected ImageService $imageService;

    public function __construct(
        FFMpegService $ffmpegService,
        MediaConversionService $conversionService,
        ImageService $imageService
    ) {
        $this->ffmpegService = $ffmpegService;
        $this->conversionService = $conversionService;
        $this->imageService = $imageService;
    }

    /**
     * @param Media $media
     *
     * @return void
     */
    public function create(Media $media): void
    {
        $path = $this->conversionService->temporaryDirectory()->path(
            config('video.thumbnail_name')
        );

        $video = $this->ffmpegService->openFile(
            $media->getPath()
        );

        $duration = $media->getCustomProperty('metadata.duration', 60);
        $timeCode = $media->getCustomProperty('frameshot', $duration / 2);

        $this->ffmpegService->createThumbnail(
            $video,
            $path,
            $timeCode,
            config('video.thumbnail_filter')
        );

        $this->imageService->optimize($path);

        $this
            ->conversionService
            ->importConversion($media, $path, config('video.thumbnail_name'));

        $media->markAsConversionGenerated('thumbnail');
    }
}
