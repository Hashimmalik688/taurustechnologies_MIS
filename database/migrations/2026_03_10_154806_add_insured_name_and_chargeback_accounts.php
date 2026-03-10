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
        // Add insured_name to journal entries
        Schema::table('ledger_journal_entries', function (Blueprint $table) {
            $table->string('insured_name')->nullable()->after('description');
        });

        // Seed: A/P Carriers + Sales Returns / Chargebacks
        DB::table('chart_of_accounts')->insertOrIgnore([
            [
                'account_code'     => '2100',
                'account_name'     => 'Accounts Payable — Carriers',
                'account_type'     => 'Liability',
                'account_category' => 'Current Liability',
                'description'      => 'Amounts owed to insurance carriers (chargeback/clawback obligations)',
                'opening_balance'  => 0,
                'current_balance'  => 0,
                'is_active'        => true,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'account_code'     => '4200',
                'account_name'     => 'Sales Returns / Chargebacks',
                'account_type'     => 'Revenue',
                'account_category' => 'Operating Revenue',
                'description'      => 'Contra-revenue: policy chargebacks and sales returns',
                'opening_balance'  => 0,
                'current_balance'  => 0,
                'is_active'        => true,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ledger_journal_entries', function (Blueprint $table) {
            $table->dropColumn('insured_name');
        });

        DB::table('chart_of_accounts')->whereIn('account_code', ['2100', '4200'])->delete();
    }
};
