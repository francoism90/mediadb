<?php

namespace App\Providers;

use App\Models\Media;
use App\Models\Tag;
use App\Models\User;
use App\Observers\MediaObserver;
use App\Observers\TagObserver;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [];

    /**
     * Register any application services.
     */
    public function register()
    {
        Route::singularResourceParameters(false);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        Media::observe(MediaObserver::class);
        Tag::observe(TagObserver::class);
        User::observe(UserObserver::class);
    }
}
