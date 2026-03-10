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
        Schema::table('ledger_journal_entries', function (Blueprint $table) {
            // Gross (full) sale amount — stored for reference only
            $table->decimal('gross_amount', 15, 2)->nullable()->after('total_debit');
            // Our share percentage (0–100)
            $table->decimal('our_share_percentage', 6, 4)->nullable()->after('gross_amount');
        });
    }

    public function down(): void
    {
        Schema::table('ledger_journal_entries', function (Blueprint $table) {
            $table->dropColumn(['gross_amount', 'our_share_percentage']);
        });
    }
};
