<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * 1. Adds leads.ledger_chargeback_paid_entry_id — FK to the recovery journal
     *    entry posted when a chargebacked lead is marked as paid.
     * 2. Adds 'chargeback_recovery' to ledger_journal_entries.type enum.
     */
    public function up(): void
    {
        // 1. Add the column to leads
        Schema::table('leads', function (Blueprint $table) {
            $table->unsignedBigInteger('ledger_chargeback_paid_entry_id')
                  ->nullable()
                  ->after('ledger_sales_return_entry_id')
                  ->comment('Journal entry ID for chargeback recovery (Dr 1200 AR / Cr 4100 Sales)');
        });

        // 2. Extend the type enum on ledger_journal_entries
        DB::statement("
            ALTER TABLE ledger_journal_entries
            MODIFY COLUMN type
            ENUM('sale','payment_received','opening_balance','general','chargeback','sales_return','chargeback_recovery')
            NOT NULL
        ");
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('ledger_chargeback_paid_entry_id');
        });

        DB::statement("
            ALTER TABLE ledger_journal_entries
            MODIFY COLUMN type
            ENUM('sale','payment_received','opening_balance','general','chargeback','sales_return')
            NOT NULL
        ");
    }
};
