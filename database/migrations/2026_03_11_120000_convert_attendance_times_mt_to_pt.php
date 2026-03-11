<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Convert attendance login_time / logout_time from naive Mountain Time
     * datetimes to Pacific Time (PT = MT - 1h).
     *
     * Background: the app previously ran in America/Denver (Mountain Time).
     * Datetimes were stored as naive MT strings in MySQL DATETIME columns.
     * After switching the app timezone to America/Los_Angeles (Pacific Time),
     * those stored values would display 1 hour ahead of reality because
     * Pacific Time is 1 hour behind Mountain Time.
     *
     * Fix: subtract 1 hour from every attendance record's login/logout times.
     * Then recalculate is_late based on the updated PT times.
     */
    public function up(): void
    {
        // Shift login_time and logout_time back 1 hour (MT → PT)
        DB::statement("
            UPDATE attendances
            SET
                login_time  = DATE_SUB(login_time,  INTERVAL 1 HOUR),
                logout_time = CASE
                                  WHEN logout_time IS NOT NULL
                                  THEN DATE_SUB(logout_time, INTERVAL 1 HOUR)
                                  ELSE NULL
                              END
            WHERE login_time IS NOT NULL
        ");

        // Recalculate is_late: late if login > 09:15 PT (matches the late_time setting)
        DB::statement("
            UPDATE attendances
            SET is_late = CASE
                              WHEN login_time IS NOT NULL AND TIME(login_time) > '09:15:00' THEN 1
                              ELSE 0
                          END
        ");

        // Update settings descriptions
        DB::table('settings')->where('key', 'office_start_time')->update([
            'description' => 'Official office start time (Pacific Time)',
        ]);
        DB::table('settings')->where('key', 'office_end_time')->update([
            'description' => 'Official office end time (Pacific Time)',
        ]);
    }

    /**
     * Reverse: add 1 hour back (PT → MT), and restore description.
     */
    public function down(): void
    {
        DB::statement("
            UPDATE attendances
            SET
                login_time  = DATE_ADD(login_time,  INTERVAL 1 HOUR),
                logout_time = CASE
                                  WHEN logout_time IS NOT NULL
                                  THEN DATE_ADD(logout_time, INTERVAL 1 HOUR)
                                  ELSE NULL
                              END
            WHERE login_time IS NOT NULL
        ");

        // Recalculate is_late for MT
        DB::statement("
            UPDATE attendances
            SET is_late = CASE
                              WHEN login_time IS NOT NULL AND TIME(login_time) > '09:15:00' THEN 1
                              ELSE 0
                          END
        ");

        DB::table('settings')->where('key', 'office_start_time')->update([
            'description' => 'Official office start time (Mountain Time)',
        ]);
        DB::table('settings')->where('key', 'office_end_time')->update([
            'description' => 'Official office end time (Mountain Time)',
        ]);
    }
};
