<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Seed 8 standard Expense accounts (5100–5900) to enable P&L reporting.
 */
return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $accounts = [
            ['account_code' => '5100', 'account_name' => 'Salaries & Wages',    'account_type' => 'Expense', 'account_category' => 'Operating Expense'],
            ['account_code' => '5200', 'account_name' => 'Rent & Utilities',    'account_type' => 'Expense', 'account_category' => 'Operating Expense'],
            ['account_code' => '5300', 'account_name' => 'Office & Admin',      'account_type' => 'Expense', 'account_category' => 'Operating Expense'],
            ['account_code' => '5400', 'account_name' => 'Marketing & Ads',     'account_type' => 'Expense', 'account_category' => 'Operating Expense'],
            ['account_code' => '5500', 'account_name' => 'Travel & Transport',  'account_type' => 'Expense', 'account_category' => 'Operating Expense'],
            ['account_code' => '5600', 'account_name' => 'Technology & SaaS',   'account_type' => 'Expense', 'account_category' => 'Operating Expense'],
            ['account_code' => '5700', 'account_name' => 'Professional Fees',   'account_type' => 'Expense', 'account_category' => 'Operating Expense'],
            ['account_code' => '5900', 'account_name' => 'Miscellaneous',       'account_type' => 'Expense', 'account_category' => 'Operating Expense'],
        ];

        foreach ($accounts as $account) {
            // Only insert if the code doesn't already exist
            $exists = DB::table('chart_of_accounts')
                ->where('account_code', $account['account_code'])
                ->exists();

            if (!$exists) {
                DB::table('chart_of_accounts')->insert(array_merge($account, [
                    'current_balance' => 0.00,
                    'is_active'       => true,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ]));
            }
        }
    }

    public function down(): void
    {
        DB::table('chart_of_accounts')
            ->whereIn('account_code', ['5100','5200','5300','5400','5500','5600','5700','5900'])
            ->delete();
    }
};
