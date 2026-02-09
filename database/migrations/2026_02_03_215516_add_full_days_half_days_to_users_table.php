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
            // Add full_days and half_days columns for payroll tracking
            // These replace the single working_days_monthly field with more granular attendance tracking
            $table->integer('full_days')->default(0)->after('working_days_monthly')->comment('Number of full working days attended');
            $table->integer('half_days')->default(0)->after('full_days')->comment('Number of half working days attended (2 half days = 1 absent for punctuality)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['full_days', 'half_days']);
        });
    }
};
