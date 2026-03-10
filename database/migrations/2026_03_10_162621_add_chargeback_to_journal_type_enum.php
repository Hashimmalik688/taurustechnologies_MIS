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
        DB::statement("ALTER TABLE ledger_journal_entries MODIFY COLUMN type ENUM('sale','payment_received','opening_balance','general','chargeback') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE ledger_journal_entries MODIFY COLUMN type ENUM('sale','payment_received','opening_balance','general') NOT NULL");
    }
};
