<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('zoom_webhook_logs', function (Blueprint $table) {
            // Mean Opinion Score (1.0–5.0) from GET /phone/metrics/call_logs
            $table->float('mos', 3, 1)->nullable()->after('call_cost');
        });
    }

    public function down(): void
    {
        Schema::table('zoom_webhook_logs', function (Blueprint $table) {
            $table->dropColumn('mos');
        });
    }
};
