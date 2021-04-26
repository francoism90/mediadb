<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Support\Collection;

class MediaSpriteService
{
    public function __construct(
        protected MediaStreamService $mediaStreamService
    ) {
    }

    /**
     * @param Media $media
     *
     * @return string
     */
    public function create(Media $media): string
    {
        $interval = config('media.sprite_intval', 30);
        $params = config('media.sprite_params', 'w150-h100.jpg');

        $range = range(0, $media->duration, $interval);

        $vtt = "WEBVTT\n\n";

        foreach ($range as $time) {
            $startTime = gmdate('H:i:s.v', $time);
            $endTime = gmdate('H:i:s.v', ($time + $interval));

            $frame = [
                'start' => $startTime,
                'end' => $endTime,
                'url' => $this->getThumbnailUrl($media, $time * 1000, $params)
            ];

            $vtt .= "{$startTime} --> {$endTime}\n";
            $vtt .= json_encode($frame, JSON_UNESCAPED_SLASHES)."\n\n";
        }

        return $vtt;
    }

    /**
     * @param Media $media
     * @param float $offset
     *
     * @return string
     */
    protected function getThumbnailUrl(Media $media, float $offset, string $params): string
    {
        return $this
            ->mediaStreamService
            ->getMappingUrl(
                'thumb',
                "thumb-{$offset}-{$params}",
                ['media' => $media]
            );
    }
}
