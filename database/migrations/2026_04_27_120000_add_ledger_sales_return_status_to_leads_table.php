<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add ledger_sales_return_status tracking to the leads table.
     *
     * Previously, marking a sale as chargeback auto-posted a Sales Return entry
     * to the accounting ledger. Some insurance companies do not claw back money
     * for up to 60 days, so the auto-post has been removed. Instead, this field
     * tracks whether the sales return has been manually confirmed to the ledger.
     *
     * pending  → chargeback marked, sales return not yet posted to ledger
     * posted   → sales return entry has been confirmed and posted to ledger
     */
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('ledger_sales_return_status', 20)
                  ->default('pending')
                  ->after('ledger_sales_return_entry_id')
                  ->comment('pending = awaiting manual ledger post; posted = sales return entry confirmed to ledger');

            $table->timestamp('ledger_sales_return_posted_at')
                  ->nullable()
                  ->after('ledger_sales_return_status');

            $table->unsignedBigInteger('ledger_sales_return_posted_by_id')
                  ->nullable()
                  ->after('ledger_sales_return_posted_at');
        });

        // Backfill: any existing chargeback that already has a sales return entry
        // is considered already posted.
        DB::statement("
            UPDATE leads
            SET ledger_sales_return_status = 'posted'
            WHERE ledger_sales_return_entry_id IS NOT NULL
        ");
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn([
                'ledger_sales_return_status',
                'ledger_sales_return_posted_at',
                'ledger_sales_return_posted_by_id',
            ]);
        });
    }
};
