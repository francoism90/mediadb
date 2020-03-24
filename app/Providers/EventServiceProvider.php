<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAdded' => [
            'App\Listeners\Media\SetAttributes',
        ],

        'Spatie\MediaLibrary\Conversions\Events\ConversionHasBeenCompleted' => [
            'App\Listeners\Media\CreatePreviewClip',
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return true;
    }
}
