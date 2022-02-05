<?php

namespace App\Providers;

use Illuminate\Broadcasting\Broadcasters\PusherBroadcaster;
use Illuminate\Broadcasting\BroadcastManager;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;
use Psr\Log\LoggerInterface;
use Pusher\Pusher;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(BroadcastManager $broadcastManager): void
    {
        Broadcast::routes([
            'prefix' => 'api/v1',
            'middleware' => ['api', 'auth:sanctum'],
        ]);

        // @doc https://github.com/soketi/soketi/issues/191#issuecomment-1028877964
        $broadcastManager->extend('pusher', function ($app, $config) {
            $pusher = new Pusher(
                $config['key'], $config['secret'],
                $config['app_id'], $config['options'] ?? [],
                new \GuzzleHttp\Client($config['client_options']) ?? []
            );

            if ($config['log'] ?? false) {
                $pusher->setLogger($this->app->make(LoggerInterface::class));
            }

            return new PusherBroadcaster($pusher);
        });

        require base_path('routes/channels.php');
    }
}
