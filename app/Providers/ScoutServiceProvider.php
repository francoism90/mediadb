<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Scout\Builder;

class ScoutServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Builder::macro('simplePaginateFilter', function () {
            $paginator = call_user_func_array([$this, 'paginate'], func_get_args());
            $paginator->appends([]);

            return $paginator;
        });
    }
}
