<?php

namespace App\Actions\Video;

use App\Events\Video\VideoHasBeenUpdated;
use App\Models\Video;

class UpdateVideoThumbnail
{
    public function __invoke(Video $video): void
    {
        app(UpdateVideoClips::class)($video, [
            'thumbnail' => $video->extra_attributes->get('capture_time', 1),
        ]);

        event(new VideoHasBeenUpdated($video));
    }
}
