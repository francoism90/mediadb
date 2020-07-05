<?php

namespace App\Listeners\Media;

use App\Jobs\Media\CreatePreview;
use App\Jobs\Media\SetAttributes;
use App\Jobs\Media\SetProcessed;
use App\Models\Media;
use Spatie\MediaLibrary\Conversions\Events\ConversionHasBeenCompleted;

class Process
{
    /**
     * @var Media
     */
    protected $media;

    /**
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param object $event
     *
     * @return void
     */
    public function handle(ConversionHasBeenCompleted $event)
    {
        // Already processed media
        if ($event->media->hasEverHadStatus('processed')) {
            return;
        }

        // e.g. video/mp4 => video
        $type = strtok($event->media->mime_type, '/');

        switch ($type) {
            case 'video':
                SetAttributes::withChain([
                    new CreatePreview($event->media),
                    new SetProcessed($event->media),
                ])->dispatch($event->media)->allOnQueue('media');
                break;
        }
    }
}
