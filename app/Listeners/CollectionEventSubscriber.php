<?php

namespace App\Listeners;

use App\Events\Collection\HasBeenUpdated;
use App\Models\Tag;

class CollectionEventSubscriber
{
    /**
     * Handle collection updated events.
     */
    public function handleCollectionUpdated($event)
    {
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
            [CollectionEventSubscriber::class, 'handleCollectionUpdated']
        );
    }
}
