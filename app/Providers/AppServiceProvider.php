<?php

namespace App\Providers;

use App\Models\Tag;
use App\Models\User;
use App\Models\Video;
use App\Support\Sanitizer\SlugFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Spatie\PrefixedIds\PrefixedIds;

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
        PrefixedIds::registerModels([
            'tag_' => Tag::class,
            'user_' => User::class,
            'video_' => Video::class,
        ]);

        \Sanitizer::extend('slug', SlugFilter::class);

        Model::preventLazyLoading(!app()->isProduction());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    }
}
