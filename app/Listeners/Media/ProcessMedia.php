<?php

namespace App\Listeners\Media;

use App\Actions\Media\RegenerateMedia;
use App\Events\Media\MediaHasBeenAdded;
use App\Events\Media\MediaHasBeenUpdated;

class ProcessMedia
{
    public function handle(
        MediaHasBeenAdded | MediaHasBeenUpdated $event
    ): void {
        app(RegenerateMedia::class)->execute($event->media);
    }
}
