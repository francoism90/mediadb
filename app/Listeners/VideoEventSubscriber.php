<?php

namespace App\Listeners;

use App\Events\Video\HasBeenUpdated;
use App\Models\Collection;
use App\Models\Tag;
use Illuminate\Events\Dispatcher;

class VideoEventSubscriber
{
    /**
     * @param mixed $event
     *
     * @return void
     */
    public function handleVideoUpdated($event): void
    {
        Collection::flushQueryCache();
        Tag::flushQueryCache();
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param Dispatcher $events
     *
     * @return void
     */
    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            HasBeenUpdated::class,
            [VideoEventSubscriber::class, 'handleVideoUpdated']
        );
    }
}
