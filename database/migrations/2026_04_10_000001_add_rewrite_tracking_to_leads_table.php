<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            // Track which original lead spawned this rewrite sale
            $table->unsignedBigInteger('rewrite_source_lead_id')->nullable()->after('is_rewrite');
            // When set, this rewrite sale has been "sent back to retention" and should hide from sales
            $table->timestamp('rewrite_sent_back_at')->nullable()->after('rewrite_source_lead_id');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['rewrite_source_lead_id', 'rewrite_sent_back_at']);
        });
    }
};
