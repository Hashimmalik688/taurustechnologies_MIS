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
            // Add partner_id column after assigned_agent_id for better organization
            $table->foreignId('partner_id')->nullable()->after('assigned_agent_set_at')->constrained('partners')->onDelete('set null');
            $table->timestamp('partner_set_at')->nullable()->after('partner_id');
            $table->index('partner_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['partner_id']);
            $table->dropColumn(['partner_id', 'partner_set_at']);
        });
    }
};
