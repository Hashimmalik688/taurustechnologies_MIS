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
        Schema::table('leads', function (Blueprint $table) {
            // Tracks how many times a closer re-submitted sale on an already-sold lead
            $table->unsignedSmallInteger('resale_count')->default(0)->after('sale_date');
            // JSON log: [{"closer_id":X,"closer_name":"...","submitted_at":"..."}]
            $table->json('resale_log')->nullable()->after('resale_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['resale_count', 'resale_log']);
        });
    }
};
