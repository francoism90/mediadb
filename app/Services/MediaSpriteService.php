<?php

namespace App\Services;

use App\Models\Media;

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
        $interval = config('media.sprite_intval', 15);
        $params = config('media.sprite_params', 'w160-h90.jpg');

        $range = range(0, ceil($media->duration), $interval);

        $vtt = "WEBVTT\n\n";

        foreach ($range as $time) {
            $timeStamp = floor($time);
            $startTime = gmdate('H:i:s.v', $timeStamp);
            $endTime = gmdate('H:i:s.v', $timeStamp + $interval);

            $frame = [
                'start' => $startTime,
                'end' => $endTime,
                'url' => $this->getThumbnailUrl($media, $timeStamp * 1000, $params),
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
