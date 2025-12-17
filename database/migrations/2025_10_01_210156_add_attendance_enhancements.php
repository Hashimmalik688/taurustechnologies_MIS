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
        Schema::table('attendances', function (Blueprint $table) {
            $table->integer('working_hours')->default(0)->after('status');
            $table->integer('punctuality_bonus_count')->default(0)->after('working_hours');
            $table->boolean('is_late')->default(false)->after('punctuality_bonus_count');
            $table->time('expected_login_time')->default('09:00:00')->after('is_late');
            $table->integer('late_minutes')->default(0)->after('expected_login_time');

            $table->index('is_late');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex(['is_late']);
            $table->dropColumn([
                'working_hours',
                'punctuality_bonus_count',
                'is_late',
                'expected_login_time',
                'late_minutes'
            ]);
        });
    }
};
