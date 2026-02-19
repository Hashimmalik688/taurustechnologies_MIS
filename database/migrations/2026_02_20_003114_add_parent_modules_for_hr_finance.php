<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add parent-level modules for HR Operations and Finance & Accounts
     * so they can be gated with @canViewModule in the sidebar.
     */
    public function up(): void
    {
        $modules = [
            [
                'name'        => 'HR Operations',
                'slug'        => 'hr',
                'description' => 'HR Operations hub — employee management, attendance, dock, and holidays',
                'category'    => 'HR Operations',
                'sort_order'  => 135,
                'is_active'   => true,
            ],
            [
                'name'        => 'Finance & Accounts',
                'slug'        => 'finance',
                'description' => 'Finance & Accounts hub — chart of accounts, ledger, petty cash, payroll, PABS tickets',
                'category'    => 'Finance & Accounts',
                'sort_order'  => 235,
                'is_active'   => true,
            ],
        ];

        foreach ($modules as $module) {
            DB::table('modules')->updateOrInsert(
                ['slug' => $module['slug']],
                array_merge($module, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('modules')->whereIn('slug', ['hr', 'finance'])->delete();
    }
};
