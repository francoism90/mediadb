<?php

namespace App\Actions\Video;

use App\Events\Video\VideoHasBeenDeleted;
use App\Models\Video;

class DeleteVideo
{
    public function __invoke(Video $video): void
    {
        $video->deleteOrFail();

        VideoHasBeenDeleted::dispatch($video);
    }
}
