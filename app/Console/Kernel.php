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
        $siteId = config('cosmetica.site_id', 1);
        $schedule->command("itemlist:search {$siteId} once")->everyFiveMinutes();
        $schedule->command("itemlist:search {$siteId} daily")->dailyAt('00:00');
        $schedule->command("itemlist:search {$siteId} weekly")->weeklyOn(1, '00:00');
        $schedule->command("itemlist:search {$siteId} monthly")->monthlyOn(1, '00:00');
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
