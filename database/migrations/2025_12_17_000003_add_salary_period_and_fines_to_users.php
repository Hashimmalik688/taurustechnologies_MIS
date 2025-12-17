<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Salary period settings
            $table->date('salary_start_date')->nullable()->after('joining_date');
            $table->date('salary_end_date')->nullable()->after('salary_start_date');
            $table->tinyInteger('payday_date')->default(5)->after('salary_end_date')->comment('Day of month for salary payment (1-31)');
            
            // Fine/Dock settings
            $table->decimal('fine_per_absence', 8, 2)->default(0)->after('punctuality_bonus')->comment('Fine amount per absence/leave');
            $table->decimal('fine_per_late', 8, 2)->default(0)->after('fine_per_absence')->comment('Fine amount per late arrival');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['salary_start_date', 'salary_end_date', 'payday_date', 'fine_per_absence', 'fine_per_late']);
        });
    }
};
