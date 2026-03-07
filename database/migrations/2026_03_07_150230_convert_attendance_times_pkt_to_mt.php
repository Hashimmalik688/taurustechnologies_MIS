<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Convert attendance login_time / logout_time from naive PKT datetimes to
     * Mountain Time (MT = PKT - 12h).
     *
     * Background: the app previously ran in Asia/Karachi (UTC+5). Employees
     * work a Pakistan-evening shift (≈19:00–04:00 PKT) which is the same as
     * ≈07:00–16:00 MT (UTC-7). Datetimes were stored as naive PKT strings in
     * MySQL DATETIME columns (no UTC conversion). After the app timezone was
     * changed to America/Denver, those stored values display as if they are MT,
     * so "19:00 PKT" incorrectly shows as "7 PM MT" instead of "7 AM MT".
     *
     * Fix: subtract 12 hours from every row whose login_time has an hour >= 12
     * (i.e. real PKT evening-shift records). Rows with login hour < 12 are
     * seeded/dummy 09:00–18:00 records and are left untouched.
     *
     * After the conversion, recalculate is_late based on the new MT times.
     */
    public function up(): void
    {
        // Shift login_time and logout_time back 12 hours for all real records
        DB::statement("
            UPDATE attendances
            SET
                login_time  = DATE_SUB(login_time,  INTERVAL 12 HOUR),
                logout_time = CASE
                                  WHEN logout_time IS NOT NULL
                                  THEN DATE_SUB(logout_time, INTERVAL 12 HOUR)
                                  ELSE NULL
                              END
            WHERE HOUR(login_time) >= 12
        ");

        // Recalculate is_late: late if login > 09:15 MT (matches the late_time setting)
        DB::statement("
            UPDATE attendances
            SET is_late = CASE
                              WHEN login_time IS NOT NULL AND TIME(login_time) > '09:15:00' THEN 1
                              ELSE 0
                          END
        ");
    }

    /**
     * Reverse: add 12 hours back, and reset is_late to 0 (conservative default).
     */
    public function down(): void
    {
        DB::statement("
            UPDATE attendances
            SET
                login_time  = DATE_ADD(login_time,  INTERVAL 12 HOUR),
                logout_time = CASE
                                  WHEN logout_time IS NOT NULL
                                  THEN DATE_ADD(logout_time, INTERVAL 12 HOUR)
                                  ELSE NULL
                              END
            WHERE HOUR(login_time) < 12
        ");

        DB::statement("UPDATE attendances SET is_late = 0");
    }
};
