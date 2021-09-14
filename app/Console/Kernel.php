<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [];

    /**
     * Get the timezone that should be used by default for scheduled events.
     */
    protected function scheduleTimezone(): string
    {
        return config('app.timezone', 'UTC');
    }

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('horizon:snapshot')
                 ->withoutOverlapping()
                 ->everyFiveMinutes()
                 ->runInBackground();

        $schedule->command('telescope:prune')
                 ->withoutOverlapping()
                 ->dailyAt('02:00')
                 ->runInBackground();

        $schedule->command('websockets:clean')
                 ->withoutOverlapping()
                 ->dailyAt('02:00')
                 ->runInBackground();

        $schedule->command('video:regenerate')
                 ->withoutOverlapping()
                 ->dailyAt('03:00')
                 ->environments(['staging', 'production'])
                 ->runInBackground();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
