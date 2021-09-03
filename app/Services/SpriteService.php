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

    public function create(Video $model, string $token): string
    {
        $this->vodService->streamer->setToken($token);

        $path = $this->conversionService->temporaryDirectory()->path(
            config('api.conversions.sprite.path')
        );

        $contents = $this->generateContents($model);

        $this->filesystem->put($path, $contents, true);

        return $path;
    }

    protected function generateContents(Video $video): string
    {
        $duration = ceil($video->clip?->duration ?? 0);

        $steps = config('api.conversions.sprite.steps', 5);

        $range = collect(range(1, $duration, $steps));

        $vtt = "WEBVTT\n\n";

        foreach ($range as $timeCode) {
            $next = $range->after($timeCode, $duration);

            $vtt .= sprintf(
                "%s --> %s\n%s\n\n",
                gmdate('H:i:s.v', $timeCode),
                gmdate('H:i:s.v', $next),
                $this->generateUrl($timeCode),
            );
        }

        return $vtt;
    }

    protected function generateUrl(float $timeCode)
    {
        $uri = sprintf(
            'thumb-%s-w%d-h%d.jpg',
            $timeCode * 1000,
            config('api.conversions.sprite.width', 160),
            config('api.conversions.sprite.height', 90),
        );

        return $this->vodService->streamer->getUrl('thumb', $uri);
    }
}
