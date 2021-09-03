<?php

namespace App\Services;

class SpriteService
{
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
