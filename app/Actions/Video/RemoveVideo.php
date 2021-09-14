<?php

namespace App\Actions\Video;

use App\Events\Video\VideoHasBeenDeleted;
use App\Models\Video;

class RemoveVideo
{
    public function __invoke(Video $video): void
    {
        throw_if(!$video->delete());

        event(new VideoHasBeenDeleted($video));
    }
}
