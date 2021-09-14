<?php

namespace App\Actions\Video;

use App\Events\Video\VideoHasBeenDeleted;
use App\Models\Video;

class RemoveVideo
{
    public function __invoke(Video $video): void
    {
        $video->deleteOrFail();

        event(new VideoHasBeenDeleted($video));
    }
}
