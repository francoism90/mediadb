<?php

namespace App\Services;

use App\Models\Video;
use Illuminate\Filesystem\Filesystem;

class SpriteService
{
    public function __construct(
        protected VodService $vodService,
        protected ConversionService $conversionService,
        protected Filesystem $filesystem
    ) {
    }

    public function create(Video $video): string
    {
        $path = $this->conversionService->temporaryDirectory()->path(
            config('api.conversions.sprite.path')
        );

        $contents = $this->generateMapping($video);

        $this->filesystem->put($path, $contents, true);

        return $path;
    }

    protected function generateMapping(Video $video): string
    {
        $duration = ceil($video->clip?->duration ?? 0);

        $steps = config('api.conversions.sprite.steps', 10);

        $range = collect(range(0, $duration, $steps));

        $vtt = "WEBVTT\n\n";

        foreach ($range as $value) {
            $next = $range->after($value, $duration);

            $vtt .= sprintf(
                "%s --> %s\n%s\n\n",
                gmdate('H:i:s.v', $value),
                gmdate('H:i:s.v', $next),
                $this->getUrl($video, $value),
            );
        }

        return $vtt;
    }

    protected function getUrl(Video $video, float $time)
    {
        $uri = sprintf(
            'thumb-%s-w%d-h%d.jpg',
            config('api.conversions.sprite.width', 160),
            config('api.conversions.sprite.height', 90),
            floor($time * 1000)
        );

        return $this->vodService->generateUrl('thumb', $uri, [
            'video' => $video,
        ]);
    }
}
