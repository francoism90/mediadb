<?php

namespace App\Actions\Vod;

use App\Models\Video;

class CreateSpriteVtt
{
    public function execute(Video $video): void
    {
    }

    protected function getDuration(Video $video): float
    {
        return $video->getFirstMedia('clip')?->getCustomProperty(
            'duration',
            0
        );
    }
}
