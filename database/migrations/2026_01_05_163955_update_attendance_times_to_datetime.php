<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations - Convert TIME columns to DATETIME for overnight shifts
     */
    public function up(): void
    {
        // Add temporary datetime columns
        Schema::table('attendances', function (Blueprint $table) {
            $table->datetime('login_time_temp')->nullable()->after('date');
            $table->datetime('logout_time_temp')->nullable()->after('login_time_temp');
        });

        // Copy existing data, converting time to datetime
        DB::statement("
            UPDATE attendances 
            SET 
                login_time_temp = CASE 
                    WHEN login_time IS NOT NULL 
                    THEN CONCAT(date, ' ', login_time)
                    ELSE NULL 
                END,
                logout_time_temp = CASE 
                    WHEN logout_time IS NOT NULL 
                    THEN CONCAT(date, ' ', logout_time)
                    ELSE NULL 
                END
        ");

        // Drop old time columns
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['login_time', 'logout_time']);
        });

        // Rename temp columns
        Schema::table('attendances', function (Blueprint $table) {
            $table->renameColumn('login_time_temp', 'login_time');
            $table->renameColumn('logout_time_temp', 'logout_time');
        });

        // Reorder columns
        DB::statement("ALTER TABLE attendances MODIFY COLUMN login_time DATETIME NULL AFTER date");
        DB::statement("ALTER TABLE attendances MODIFY COLUMN logout_time DATETIME NULL AFTER login_time");
    }

    /**
     * Reverse the migrations
     */
    public function down(): void
    {
        // Add temporary time columns
        Schema::table('attendances', function (Blueprint $table) {
            $table->time('login_time_temp')->nullable()->after('date');
            $table->time('logout_time_temp')->nullable()->after('login_time_temp');
        });

        // Extract time portion
        DB::statement("
            UPDATE attendances 
            SET 
                login_time_temp = CASE 
                    WHEN login_time IS NOT NULL 
                    THEN TIME(login_time)
                    ELSE NULL 
                END,
                logout_time_temp = CASE 
                    WHEN logout_time IS NOT NULL 
                    THEN TIME(logout_time)
                    ELSE NULL 
                END
        ");

        // Drop datetime columns
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['login_time', 'logout_time']);
        });

        // Rename back
        Schema::table('attendances', function (Blueprint $table) {
            $table->renameColumn('login_time_temp', 'login_time');
            $table->renameColumn('logout_time_temp', 'logout_time');
        });

        DB::statement("ALTER TABLE attendances MODIFY COLUMN login_time TIME NULL AFTER date");
        DB::statement("ALTER TABLE attendances MODIFY COLUMN logout_time TIME NULL AFTER login_time");
    }
};
