<?php

namespace App\Actions\Video;

use App\Events\Video\VideoHasBeenDeleted;
use App\Models\Video;

class RemoveVideo
{
    public function execute(Video $video): Video
    {
        throw_if(!$video->delete());

        event(new VideoHasBeenDeleted($video));

        return $video;
    }
}
