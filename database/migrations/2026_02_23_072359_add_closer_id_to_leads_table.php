<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->unsignedBigInteger('closer_id')->nullable()->after('closer_name');
            $table->index('closer_id', 'idx_leads_closer_id');
        });

        // Backfill closer_id from closer_name by matching users table
        DB::statement("
            UPDATE leads l
            INNER JOIN users u ON u.name = l.closer_name
            SET l.closer_id = u.id
            WHERE l.closer_name IS NOT NULL AND l.closer_id IS NULL
        ");

        // Note: Do NOT backfill sale_at for 'closed' status leads.
        // 'closed' means "lead form submitted by closer", not a completed sale.
        // Real sales are recorded via submitSale() which sets sale_at explicitly.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex('idx_leads_closer_id');
            $table->dropColumn('closer_id');
        });
    }
};
