<?php

namespace App\Actions\Video;

use App\Models\Video;
use Illuminate\Support\Collection;

class CreateSpriteTrack
{
    public function __invoke(Video $video): Collection
    {
        $duration = $this->getDuration($video);

        $sequences = $this->getSpriteSequences($duration);

        $sections = collect();

        foreach ($sequences as $sequence) {
            $next = $sequences->after($sequence, $duration);

            $sections->push([
                'start_time' => gmdate('H:i:s.v', $sequence),
                'end_time' => gmdate('H:i:s.v', $next),
                'contents' => [
                    'kind' => 'thumbnail',
                    'src' => $video->frameCaptureUrl($sequence),
                ],
            ]);
        }

        return $sections;
    }

    protected function getSpriteSequences(float $duration = 0): Collection
    {
        return collect(range(0, $duration, 10));
    }

    protected function getDuration(Video $video): ?float
    {
        return $video->getFirstMedia('clips')?->getCustomProperty('duration');
    }
}
