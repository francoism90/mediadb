<?php

namespace App\Services;

use App\Models\Media;

class SpriteService
{
    public function __construct(
        protected StreamService $streamService
    ) {
    }

    public function create(Media $media): string
    {
        $vtt = "WEBVTT\n\n";

        $collection = collect($this->generateRange($media->duration));

        foreach ($collection->values() as $index => $time) {
            $offset = $time * 1000;

            $thumbnailUrl = $this->streamService->getMappingUrl(
                'thumb',
                "thumb-{$offset}-w160-h90.jpg",
                ['media' => $media]
            );

            $next = $collection->get(++$index, $media->duration);

            $startTime = gmdate('H:i:s.v', $time);
            $endTime = gmdate('H:i:s.v', $next);

            $vtt .= "{$startTime} --> {$endTime}\n";
            $vtt .= "{$thumbnailUrl}\n\n";
        }

        return $vtt;
    }

    protected function generateRange(float $duration = 0): array
    {
        $divider = config('media.sprite_intval', 30);

        $base = $duration / $divider;

        $range = [];

        for ($i = 0; $i <= $divider; ++$i) {
            $range[] = round($i * $base);
        }

        return $range;
    }
}
