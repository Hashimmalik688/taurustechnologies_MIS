<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChartOfAccount;

class SystemAccountsSeeder extends Seeder
{
    /**
     * Seed the minimum chart-of-accounts needed for the double-entry
     * ledger module plus register the 'accounting' module for permissions.
     *
     * Safe to re-run — uses updateOrCreate throughout.
     */
    public function run(): void
    {
        // ── 1. System Accounts ─────────────────────────────────────────────

        $accounts = [
            [
                'account_code'     => '1100',
                'account_name'     => 'Cash / Bank',
                'account_type'     => 'Asset',
                'account_category' => 'Current Asset',
                'description'      => 'Cash in hand and bank deposits',
                'opening_balance'  => 0,
                'current_balance'  => 0,
                'is_active'        => true,
            ],
            [
                'account_code'     => '1200',
                'account_name'     => 'Accounts Receivable – Partners',
                'account_type'     => 'Asset',
                'account_category' => 'Current Asset',
                'description'      => 'Amounts owed by partners for insurance sales',
                'opening_balance'  => 0,
                'current_balance'  => 0,
                'is_active'        => true,
            ],
            [
                'account_code'     => '3900',
                'account_name'     => 'Opening Balance Equity',
                'account_type'     => 'Equity',
                'account_category' => 'Owner Equity',
                'description'      => 'Counter-account used when recording opening balances',
                'opening_balance'  => 0,
                'current_balance'  => 0,
                'is_active'        => true,
            ],
            [
                'account_code'     => '4100',
                'account_name'     => 'Sales / Commission Income',
                'account_type'     => 'Revenue',
                'account_category' => 'Operating Revenue',
                'description'      => 'Income from insurance policy sales and commissions',
                'opening_balance'  => 0,
                'current_balance'  => 0,
                'is_active'        => true,
            ],
        ];

        foreach ($accounts as $account) {
            ChartOfAccount::updateOrCreate(
                ['account_code' => $account['account_code']],
                $account
            );
        }

        $this->command->info('System accounts seeded (1100, 1200, 3900, 4100).');

        // ── 2. Register the 'accounting' module ────────────────────────────

        DB::table('modules')->updateOrInsert(
            ['slug' => 'accounting'],
            [
                'name'      => 'Accounting Ledger',
                'slug'      => 'accounting',
                'is_active' => true,
            ]
        );

        $this->command->info("'accounting' module registered.");
    }
}
