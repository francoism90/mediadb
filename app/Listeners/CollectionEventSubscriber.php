<?php

namespace App\Listeners;

use App\Events\Collection\HasBeenUpdated;
use App\Models\Tag;
use Illuminate\Events\Dispatcher;

class CollectionEventSubscriber
{
    /**
     * @param mixed $event
     *
     * @return void
     */
    public function handleCollectionUpdated($event): void
    {
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
            [CollectionEventSubscriber::class, 'handleCollectionUpdated']
        );
    }
}
