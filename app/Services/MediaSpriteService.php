<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Support\Collection;

class MediaSpriteService
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
            config('video.sprite_name')
        );

        // Create frame collection
        $frames = $this->createFrames($media);

        // Add each image for processing
        $imagick = new \Imagick();

        // Save sprite collection
        $sprite = $frames->map(function ($item, $key) use ($imagick) {
            $imagick->addImage(
                new \Imagick($item['path'])
            );

            return collect($item)->except(['path'])->toArray();
        });

        // Create montage
        $montage = $imagick->montageImage(
            new \ImagickDraw(),
            config('video.sprite_columns').'x',
            config('video.sprite_width').'x'.config('video.sprite_height').'!',
            \Imagick::MONTAGEMODE_CONCATENATE,
            '0'
        );

        $montage->writeImage($path);

        $this->imageService->optimize($path);

        $this
            ->conversionService
            ->importConversion($media, $path, config('video.sprite_name'));

        $media->setCustomProperty('sprite', $sprite);
        $media->markAsConversionGenerated('sprite');
    }

    /**
     * @param Media $media
     *
     * @return Collection
     */
    protected function createFrames(Media $media): Collection
    {
        $frames = collect();

        $frameCount = 1;
        $framePositionX = 0;
        $framePositionY = 0;

        $video = $this->ffmpegService->openFile(
            $media->getPath()
        );

        $duration = $media->getCustomProperty('metadata.duration', 60);

        $timeCodes = range(0, $duration, config('video.sprite_interval', 10));

        $temporaryDirectory = $this->conversionService->temporaryDirectory();

        foreach ($timeCodes as $timeCode) {
            $path = $temporaryDirectory->path("$frameCount.jpg");

            $this->ffmpegService->createThumbnail(
                $video,
                $path,
                $timeCode,
                config('video.sprite_filter')
            );

            $frames->push([
                'id' => $frameCount,
                'path' => $path,
                'start' => $timeCode,
                'end' => $timeCode + config('video.sprite_interval'),
                'x' => $framePositionX,
                'y' => $framePositionY,
            ]);

            // Set next frame position
            if (0 === $frameCount % config('video.sprite_columns')) {
                $framePositionX = 0;
                $framePositionY += config('video.sprite_height');
            } else {
                $framePositionX += config('video.sprite_width');
            }

            ++$frameCount;
        }

        return $frames;
    }
}
