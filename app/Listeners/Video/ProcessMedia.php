<?php

namespace App\Listeners\Video;

use App\Events\Video\MediaHasBeenAdded;
use App\Jobs\Media\CreatePreview;
use App\Jobs\Media\CreateThumbnail;
use App\Jobs\Media\ProcessVideo;
use App\Jobs\Media\SetProcessed;

class ProcessMedia
{
    /**
     * @param object $event
     *
     * @return void
     */
    public function handle(MediaHasBeenAdded $event)
    {
        // e.g. video/mp4 => video
        $type = strtok($event->media->mime_type, '/');

        switch ($type) {
            case 'video':
                ProcessVideo::withChain([
                    new CreateThumbnail($event->media),
                    new CreatePreview($event->media),
                    new SetProcessed($event->media),
                ])->dispatch($event->media)->allOnQueue('optimize');
                break;
        }
    }
}
