<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * 1. Add 'sales_return' to ledger_journal_entries.type enum.
 * 2. Add leads.ledger_sales_return_entry_id FK column.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Extend the type enum ────────────────────────────────────────
        DB::statement("
            ALTER TABLE ledger_journal_entries
            MODIFY COLUMN type
            ENUM('sale','payment_received','opening_balance','general','chargeback','sales_return')
            NOT NULL
        ");

        // ── 2. Add FK column to leads ─────────────────────────────────────
        Schema::table('leads', function (Blueprint $table) {
            if (!Schema::hasColumn('leads', 'ledger_sales_return_entry_id')) {
                $table->unsignedBigInteger('ledger_sales_return_entry_id')
                      ->nullable()
                      ->after('ledger_journal_entry_id')
                      ->comment('FK to the sales-return journal entry posted when this lead is chargebacked');

                $table->foreign('ledger_sales_return_entry_id')
                      ->references('id')
                      ->on('ledger_journal_entries')
                      ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        // Remove the FK column from leads
        Schema::table('leads', function (Blueprint $table) {
            if (Schema::hasColumn('leads', 'ledger_sales_return_entry_id')) {
                $table->dropForeign(['ledger_sales_return_entry_id']);
                $table->dropColumn('ledger_sales_return_entry_id');
            }
        });

        // Revert type enum to original values
        DB::statement("
            ALTER TABLE ledger_journal_entries
            MODIFY COLUMN type
            ENUM('sale','payment_received','opening_balance','general','chargeback')
            NOT NULL
        ");
    }
};
