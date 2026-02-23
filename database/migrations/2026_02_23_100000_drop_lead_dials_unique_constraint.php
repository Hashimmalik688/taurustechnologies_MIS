<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drop the unique constraint so each dial attempt gets its own row.
     * Previously (lead_id, user_id) was unique, meaning re‑dialing the
     * same lead just overwrote the timestamp — defeating the purpose.
     */
    public function up(): void
    {
        Schema::table('lead_dials', function (Blueprint $table) {
            $table->dropUnique(['lead_id', 'user_id']);
            // Keep a regular composite index for fast lookups
            $table->index(['lead_id', 'user_id'], 'lead_dials_lead_user_idx');
        });
    }

    public function down(): void
    {
        Schema::table('lead_dials', function (Blueprint $table) {
            $table->dropIndex('lead_dials_lead_user_idx');
            $table->unique(['lead_id', 'user_id']);
        });
    }
};
