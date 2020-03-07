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
        'Spatie\MediaLibrary\Events\MediaHasBeenAdded' => [
            'App\Listeners\Media\SetAttributes',
        ],

        'Spatie\MediaLibrary\Events\ConversionHasBeenCompleted' => [
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
}
