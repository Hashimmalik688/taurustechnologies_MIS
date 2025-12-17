<?php

namespace App\Console\Commands;

use App\Services\AttendanceService;
use Illuminate\Console\Command;

class AutoCheckoutAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:auto-checkout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically checkout employees who have not checked out after 6 AM';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $attendanceService = app(AttendanceService::class);
        
        $this->info('Running auto-checkout for overdue attendances...');
        
        $result = $attendanceService->autoCheckoutOverdueAttendances();
        
        if ($result['success']) {
            $this->info($result['message']);
            $this->info("Checked out {$result['checked_out_count']} employee(s) at 6:00 AM.");
        } else {
            $this->warn($result['message']);
        }
        
        return 0;
    }
}
