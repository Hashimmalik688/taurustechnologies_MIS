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
    ];

    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Mark absent users daily at 7:30 PM (19:30) PKT - 30 minutes after night shift start
        // Office hours: Monday 7 PM - Saturday 5 AM (night shift)
        $schedule->command('attendance:mark-absent --cutoff=19:30')
            ->dailyAt('19:30')
            ->timezone('Asia/Karachi');
        
        // Auto-checkout disabled - checkout is now fully manual
        // Days without checkout will be marked as unpaid manually by Super Admin
        // $schedule->command('attendance:auto-checkout')
        //     ->dailyAt('05:10')
        //     ->timezone('Asia/Karachi');
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
