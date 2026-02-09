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
            // Add late_days column for tracking late arrivals within full days
            // Example: 18 full days with 3 late arrivals = full_days:18, late_days:3
            $table->integer('late_days')->default(0)->after('half_days')->comment('Number of late arrivals (counted from full days, 4+ late = no punctuality bonus)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('late_days');
        });
    }
};
