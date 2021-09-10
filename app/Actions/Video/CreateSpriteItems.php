<?php

namespace App\Actions\Video;

use App\Models\Video;
use Illuminate\Support\Collection;

class CreateSpriteItems
{
    public function execute(Video $video): Collection
    {
        $duration = $this->getDuration($video);

        $sequences = $this->getSpriteSequences($duration);

        $items = collect();

        foreach ($sequences as $sequence) {
            $next = $sequences->after($sequence, $duration);

            $items->push([
                'start_time' => gmdate('H:i:s.v', $sequence),
                'end_time' => gmdate('H:i:s.v', $next),
                'contents' => [
                    'type' => 'thumbnail',
                    'url' => $video->frameCaptureUrl($sequence),
                ],
            ]);
        }

        return $items;
    }

    protected function getSpriteSequences(float $duration = 0): Collection
    {
        return collect(range(0, $duration, 10));
    }

    protected function getDuration(Video $video): ?float
    {
        return $video
            ->getFirstMedia('clips')
            ?->getCustomProperty('duration', 0);
    }
}
