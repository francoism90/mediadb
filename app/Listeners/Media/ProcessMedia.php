<?php

namespace App\Listeners\Media;

use App\Actions\Media\RegenerateMedia;
use App\Events\Media\MediaHasBeenAdded;
use App\Events\Media\MediaHasBeenUpdated;
use App\Jobs\Media\Optimize;
use App\Jobs\Media\Process;
use App\Jobs\Media\Release;
use Illuminate\Support\Facades\Bus;

class ProcessMedia
{
    public function handle(
        MediaHasBeenAdded | MediaHasBeenUpdated $event
    ): void {
        app(RegenerateMedia::class)->execute($event->media);
    }
}
