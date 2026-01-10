<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class VerifyAttendanceSchema extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:verify-schema';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify and display attendance table schema for manual entry system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Attendance Table Schema Verification ===');
        $this->newLine();

        // Get attendances table columns
        $columns = Schema::getColumnListing('attendances');
        
        $this->info('📋 Attendances Table Columns:');
        foreach ($columns as $column) {
            $this->line("  • {$column}");
        }
        
        $this->newLine();
        
        // Get column types
        $this->info('📊 Column Details:');
        $columnDetails = DB::select('DESCRIBE attendances');
        
        $this->table(
            ['Field', 'Type', 'Null', 'Key', 'Default'],
            array_map(function($col) {
                return [
                    $col->Field,
                    $col->Type,
                    $col->Null,
                    $col->Key,
                    $col->Default ?? 'NULL'
                ];
            }, $columnDetails)
        );
        
        $this->newLine();
        
        // Verify manual entry requirements
        $this->info('✅ Manual Entry System Requirements Check:');
        
        $requiredFields = [
            'user_id' => 'Employee identifier',
            'date' => 'Shift start date',
            'login_time' => 'Shift start time',
            'logout_time' => 'Shift end time',
            'status' => 'Attendance status (present/late/absent)',
            'working_hours' => 'Auto-calculated duration',
        ];
        
        foreach ($requiredFields as $field => $description) {
            if (in_array($field, $columns)) {
                $this->line("  ✓ {$field} - {$description}");
            } else {
                // Check alternative names
                $alternativeName = $field === 'user_id' ? 'employee_id' : null;
                if ($alternativeName && in_array($alternativeName, $columns)) {
                    $this->line("  ✓ {$alternativeName} (alternative to {$field}) - {$description}");
                } else {
                    $this->error("  ✗ {$field} - MISSING!");
                }
            }
        }
        
        $this->newLine();
        
        // Show sample overnight shift calculation
        $this->info('🌙 Overnight Shift Example:');
        $this->line('  Input: Login 22:00, Logout 06:00');
        $this->line('  Logic: 22:00 on Day 1 → 06:00 on Day 2 = 8 hours');
        $this->line('  Stored Date: Day 1 (shift start date)');
        $this->line('  Working Hours: 8.0 (auto-calculated)');
        
        $this->newLine();
        $this->info('✅ Schema verification complete!');
        
        return Command::SUCCESS;
    }
}
