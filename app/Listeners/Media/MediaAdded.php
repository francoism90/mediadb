<?php

namespace App\Listeners\Media;

use App\Events\Media\HasBeenAdded;
use App\Jobs\Media\Optimize;
use App\Jobs\Media\Process;
use App\Jobs\Media\Release;
use Illuminate\Support\Facades\Bus;

class MediaAdded
{
    public function handle(HasBeenAdded $event): void
    {
        Bus::chain([
            new Process($event->media),
            new Optimize($event->media),
            new Release($event->media),
        ])->onQueue('media')->dispatch($event->media);
    }
}
