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
    protected function scheduleTimezone()
    {
        return 'Europe/Amsterdam';
    }

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('horizon:snapshot')
                 ->everyFiveMinutes()
                 ->runInBackground();

        $schedule->command('telescope:prune')
                 ->dailyAt('01:00')
                 ->runInBackground();

        $schedule->command('activitylog:clean')
                 ->dailyAt('01:00')
                 ->runInBackground();

        $schedule->command('media:cleanup')
                 ->dailyAt('03:00')
                 ->environments(['staging', 'production'])
                 ->runInBackground();

        $schedule->command('video:sync')
                 ->dailyAt('04:00')
                 ->environments(['staging', 'production'])
                 ->runInBackground();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
