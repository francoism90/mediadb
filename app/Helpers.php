<?php

use App\Notifications\BroadcastNotification;

if (!function_exists('locale')) {
    function locale(): string
    {
        return app()->getLocale();
    }
}

if (!function_exists('notify')) {
    function notify($message): void
    {
        auth()->user()->notify(new BroadcastNotification(
            $message
        ));
    }
}
