<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;
use Carbon\Carbon;

class FixAttendanceWorkingHours extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:attendance-working-hours {--dry-run : Show what would be changed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix incorrect working hours calculation in attendance records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('🧪 Running in DRY RUN mode - no changes will be made');
        } else {
            $this->info('🔧 Fixing attendance working hours...');
        }

        // Get all attendance records that have both login and logout times
        $attendances = Attendance::whereNotNull('login_time')
            ->whereNotNull('logout_time')
            ->orderBy('date')
            ->get();

        $this->info("Found {$attendances->count()} attendance records with both login and logout times");

        $fixed = 0;
        $errors = 0;
        
        $bar = $this->output->createProgressBar($attendances->count());
        $bar->start();

        foreach ($attendances as $attendance) {
            try {
                // Calculate the correct working hours
                $attendanceDate = $attendance->date ?? Carbon::today();
                
                // Parse login and logout times with the actual attendance date
                $loginTime = Carbon::parse($attendanceDate->format('Y-m-d') . ' ' . $attendance->login_time->format('H:i:s'));
                $logoutTime = Carbon::parse($attendanceDate->format('Y-m-d') . ' ' . $attendance->logout_time->format('H:i:s'));
                
                // Handle night shift - if logout is before login, add a day
                if ($logoutTime->lt($loginTime)) {
                    $logoutTime->addDay();
                }
                
                $correctWorkingHours = round($loginTime->diffInHours($logoutTime, true), 1);
                $currentWorkingHours = $attendance->working_hours ?? 0;
                
                // Only update if there's a significant difference (more than 0.1 hours)
                if (abs($correctWorkingHours - $currentWorkingHours) > 0.1) {
                    if (!$isDryRun) {
                        // Update without triggering the model events (to avoid infinite recursion)
                        Attendance::where('id', $attendance->id)
                            ->update(['working_hours' => $correctWorkingHours]);
                    }
                    
                    $fixed++;
                    
                    // Show details for significant differences
                    if (abs($correctWorkingHours - $currentWorkingHours) > 2) {
                        $this->newLine();
                        $userName = $attendance->user ? $attendance->user->name : 'Unknown';
                        $this->warn("Large correction for {$userName} on {$attendance->date->format('Y-m-d')}:");
                        $this->line("  Old: {$currentWorkingHours}h → New: {$correctWorkingHours}h");
                    }
                }
                
            } catch (\Exception $e) {
                $errors++;
                $this->newLine();
                $this->error("Error processing attendance ID {$attendance->id}: {$e->getMessage()}");
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine(2);
        
        if ($isDryRun) {
            $this->info("✅ DRY RUN completed:");
            $this->line("  - Records that would be fixed: {$fixed}");
            $this->line("  - Records with errors: {$errors}");
            $this->line("  - Records that are already correct: " . ($attendances->count() - $fixed - $errors));
            $this->newLine();
            $this->comment("To actually fix the data, run: php artisan fix:attendance-working-hours");
        } else {
            $this->info("✅ Fixed working hours for {$fixed} attendance records");
            
            if ($errors > 0) {
                $this->warn("⚠️  {$errors} records had errors during processing");
            }
            
            $this->info("🎉 Attendance working hours have been corrected!");
            $this->comment("All employee dashboards should now show correct total hours.");
        }
        
        return 0;
    }
}
