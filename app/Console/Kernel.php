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
        $schedule->command('health:check')
                  ->withoutOverlapping(30)
                  ->everyMinute()
                  ->runInBackground();

        $schedule->command('health:schedule-check-heartbeat')
                  ->withoutOverlapping(30)
                  ->everyMinute()
                  ->runInBackground();

        $schedule->command('horizon:snapshot')
                 ->withoutOverlapping(240)
                 ->everyFiveMinutes()
                 ->runInBackground();

        $schedule->command('scout:sync')
                 ->withoutOverlapping()
                 ->weeklyOn(1, '02:00')
                 ->runInBackground();

        $schedule->command('telescope:prune')
                 ->withoutOverlapping()
                 ->dailyAt('02:00')
                 ->runInBackground();

        $schedule->command('schedule-monitor:clean')
                 ->withoutOverlapping()
                 ->dailyAt('01:30')
                 ->runInBackground();

        $schedule->command('schedule-monitor:sync')
                 ->withoutOverlapping()
                 ->dailyAt('05:00')
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
