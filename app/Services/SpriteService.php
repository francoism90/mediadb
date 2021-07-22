<?php

namespace App\Services;

use App\Models\Video;

class SpriteService
{
    public function __construct(
        protected VodService $vodService
    ) {
    }

    public function create(Video $video): string
    {
        $media = $video->getFirstMedia('clip');

        $collection = collect($this->generateRange($media->duration ?? 0));

        $vtt = "WEBVTT\n\n";

        foreach ($collection->values() as $index => $time) {
            $offset = $time * 1000;

            $thumbnailUrl = $this->vodService->getTemporaryUrl(
                'thumb',
                sprintf('thumb-%s-w160-h90.jpg', $offset),
                ['media' => $media]
            );

            $next = $collection->get(++$index, $media->duration);

            $startTime = gmdate('H:i:s.v', $time);
            $endTime = gmdate('H:i:s.v', $next);

            $vtt .= sprintf('%s --> %s%s', $startTime, $endTime, PHP_EOL);
            $vtt .= "{$thumbnailUrl}\n\n";
        }

        return $vtt;
    }

    protected function generateRange(float $duration): array
    {
        return range(0, ceil($duration), config('api.sprite_intval', 30));
    }
}
