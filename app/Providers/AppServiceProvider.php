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
    public array $bindings = [];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        \Sanitizer::extend('slug', SlugFilter::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    }
}
