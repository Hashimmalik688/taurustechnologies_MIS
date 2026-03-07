<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update attendance settings from night shift (PKT) to day shift (Mountain Time)
        DB::table('settings')->where('key', 'late_time')->update([
            'value' => '09:15',
            'description' => 'Fixed time after which attendance is marked late (e.g., 09:15 or 09:15 AM)',
        ]);

        DB::table('settings')->where('key', 'shift_duration_hours')->update([
            'value' => '8',
            'description' => 'Standard shift duration in hours (e.g., 8 for a standard day shift)',
        ]);

        DB::table('settings')->where('key', 'office_start_time')->update([
            'value' => '09:00',
            'description' => 'Official office start time (Mountain Time)',
        ]);
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'late_time')->update([
            'value' => '19:15',
            'description' => 'Fixed time after which attendance is marked late (e.g., 19:15 or 07:15 PM)',
        ]);

        DB::table('settings')->where('key', 'shift_duration_hours')->update([
            'value' => '10',
            'description' => 'Standard shift duration in hours (e.g., 10 for night shift)',
        ]);

        DB::table('settings')->where('key', 'office_start_time')->update([
            'value' => '19:00',
            'description' => 'Official office start time',
        ]);
    }
};
