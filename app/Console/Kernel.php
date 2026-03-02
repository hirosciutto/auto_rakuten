<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('itemlist:search once')->everyFiveMinutes();
        $schedule->command('itemlist:search daily')->dailyAt('00:00');
        $schedule->command('itemlist:search weekly')->weeklyOn(1, '00:00');
        $schedule->command('itemlist:search monthly')->monthlyOn(1, '00:00');
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
