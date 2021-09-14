<?php

namespace App\Providers;

use App\Events\Media\MediaHasBeenAdded;
use App\Events\Media\MediaHasBeenUpdated;
use App\Listeners\Media\ProcessMedia;
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
}
