<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/api/v1';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot()
    {
        $this->configureModelBinding();
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * @return void
     */
    protected function configureModelBinding()
    {
        Route::bind('collection', function ($value) {
            return \App\Models\Collection::findByHashidOrFail($value);
        });

        Route::bind('media', function ($value) {
            return \App\Models\Media::findByHashidOrFail($value);
        });

        Route::bind('tag', function ($value) {
            return \App\Models\Tag::findByHashidOrFail($value);
        });

        Route::bind('user', function ($value) {
            return \App\Models\User::findByHashidOrFail($value);
        });

        Route::bind('video', function ($value) {
            return \App\Models\Video::findByHashidOrFail($value);
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(100);
        });
    }
}
