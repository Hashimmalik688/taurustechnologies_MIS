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
    protected $commands = [
        \App\Console\Commands\MarkAbsent::class,
        \App\Console\Commands\AutoCheckoutAttendance::class,
        \App\Console\Commands\SyncZoomCallLogs::class,
        \App\Console\Commands\SyncZoomRecordings::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Mark absent users daily at 9:30 AM PT - 30 minutes after office start
        // Office hours: Monday–Friday 9 AM–5:30 PM (Pacific Time)
        $schedule->command('attendance:mark-absent --cutoff=09:30')
            ->dailyAt('09:30')
            ->timezone('America/Los_Angeles');
        
        // Auto-checkout disabled - checkout is now fully manual
        // Days without checkout will be marked as unpaid manually by Super Admin
        // $schedule->command('attendance:auto-checkout')
        //     ->dailyAt('17:30')
        //     ->timezone('America/Los_Angeles');

        // Sync Zoom Phone call logs every minute for near-real-time accuracy
        $schedule->command('zoom:sync-call-logs --hours=2')
            ->everyMinute()
            ->withoutOverlapping()
            ->runInBackground();
            
        // Fallback: Dispatch QA jobs for any un-scored transcripts every 5 minutes
        $schedule->command('qa:score-transcribed --reset-stuck')
            ->everyFiveMinutes()
            ->withoutOverlapping()
            ->runInBackground();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
