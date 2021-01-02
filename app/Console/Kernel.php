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
     *
     * @return \DateTimeZone|string|null
     */
    protected function scheduleTimezone(): string
    {
        return 'Europe/Amsterdam';
    }

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('horizon:snapshot')
                 ->everyFiveMinutes()
                 ->runInBackground();

        $schedule->command('telescope:prune')
                 ->dailyAt('02:00')
                 ->runInBackground();

        $schedule->command('activitylog:clean')
                 ->dailyAt('02:30')
                 ->runInBackground();

        $schedule->command('media:regenerate')
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
