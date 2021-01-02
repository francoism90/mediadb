<?php

namespace App\Providers;

use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return mixed
     */
    public function boot()
    {
        Telescope::auth(function () {
            if (!auth()->guard('web')->user()) {
                throw new UnauthorizedHttpException('Unauthorized');
            }

            return true;
        });
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Telescope::night();

        $this->hideSensitiveRequestDetails();

        Telescope::filter(function (IncomingEntry $entry) {
            if ($this->app->isLocal()) {
                return true;
            }

            return $entry->isReportableException() ||
                   $entry->isFailedRequest() ||
                   $entry->isFailedJob() ||
                   $entry->isScheduledTask() ||
                   $entry->hasMonitoredTag();
        });
    }

    /**
     * Prevent sensitive request details from being logged by Telescope.
     */
    protected function hideSensitiveRequestDetails(): void
    {
        if ($this->app->isLocal()) {
            return;
        }

        Telescope::hideRequestParameters(['_token']);

        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);
    }
}
