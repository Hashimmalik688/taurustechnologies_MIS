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
        Schema::table('qa_daily_stats', function (Blueprint $table) {
            $table->integer('exceptional_count')->default(0)->after('excellent_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('qa_daily_stats', function (Blueprint $table) {
            $table->dropColumn('exceptional_count');
        });
    }
};
