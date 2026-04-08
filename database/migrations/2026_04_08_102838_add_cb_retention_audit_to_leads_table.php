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
            // No FK constraints — leads table is at MySQL's 64-key limit
            $table->timestamp('cb_sent_to_retention_at')->nullable()->after('chargeback_paid_by_id');
            $table->unsignedBigInteger('cb_sent_to_retention_by_id')->nullable()->after('cb_sent_to_retention_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['cb_sent_to_retention_at', 'cb_sent_to_retention_by_id']);
        });
    }
};
