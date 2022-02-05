<?php

namespace App\Listeners\Video;

use App\Actions\Video\RegenerateVideo;
use App\Events\Video\VideoHasBeenAdded;
use App\Events\Video\VideoHasBeenUpdated;

class ProcessVideo
{
    public function handle(
        VideoHasBeenAdded|VideoHasBeenUpdated $event
    ): void {
        app(RegenerateVideo::class)($event->video);
    }
}
