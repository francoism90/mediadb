<?php

namespace App\Listeners;

use App\Events\Video\HasBeenFavorited;
use App\Events\Video\HasBeenUpdated;
use App\Models\Tag;
use App\Models\Video;
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
        Tag::flushQueryCache();
    }

    /**
     * @param mixed $event
     *
     * @return void
     */
    public function handleVideoFavorited($event): void
    {
        Video::flushQueryCache();
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

        $events->listen(
            HasBeenFavorited::class,
            [VideoEventSubscriber::class, 'handleVideoFavorited']
        );
    }
}
