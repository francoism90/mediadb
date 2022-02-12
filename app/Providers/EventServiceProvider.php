<?php

namespace App\Providers;

use App\Events\Media\MediaHasBeenAdded;
use App\Events\Media\MediaHasBeenUpdated;
use App\Events\Video\VideoHasBeenAdded;
use App\Events\Video\VideoHasBeenUpdated;
use App\Listeners\Media\ProcessMedia;
use App\Listeners\Video\ProcessVideo;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        MediaHasBeenAdded::class => [
            ProcessMedia::class,
        ],

        MediaHasBeenUpdated::class => [
            ProcessMedia::class,
        ],

        VideoHasBeenAdded::class => [
            ProcessVideo::class,
        ],

        VideoHasBeenUpdated::class => [
            ProcessVideo::class,
        ],
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot(): void
    {
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
