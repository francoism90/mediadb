<?php

namespace App\Actions\Video;

use App\Events\Video\VideoHasBeenUpdated;
use App\Models\Video;

class UpdateVideoThumbnail
{
    public function __invoke(Video $video): void
    {
        $video->refresh();

        app(UpdateVideoClips::class)($video, [
            'thumbnail' => $video->capture_time,
        ]);

        event(new VideoHasBeenUpdated($video));
    }
}
