<?php

namespace App\Listeners;

use App\Events\Video\HasBeenUpdated;
use App\Models\Collection;
use App\Models\Tag;

class VideoEventSubscriber
{
    /**
     * Handle video updated events.
     */
    public function handleVideoUpdated($event)
    {
        Collection::flushQueryCache();
        Tag::flushQueryCache();
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param \Illuminate\Events\Dispatcher $events
     *
     * @return void
     */
    public function subscribe($events)
    {
        $events->listen(
            HasBeenUpdated::class,
            [VideoEventSubscriber::class, 'handleVideoUpdated']
        );
    }
}
