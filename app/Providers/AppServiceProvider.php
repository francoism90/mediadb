<?php

namespace App\Providers;

use App\Support\Sanitizer\SlugFilter;
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
        \Sanitizer::extend('slug', SlugFilter::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
    }
}
