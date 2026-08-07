<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Hell Cats equivalent of the "report-peregrine-team" module — same
 * PJC/Closer/Validator breakdown report, scoped to team = 'hell_cats'.
 *
 * Creates the module only — no permissions are auto-granted. Access must
 * be assigned explicitly per role via Settings → Permission Manager.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('modules')->insertOrIgnore([
            'name'        => 'Hell Cats Team Report',
            'slug'        => 'report-hell-cats-team',
            'description' => 'PJC submissions, Closer pipeline & Validator outcomes for the Hell Cats team',
            'category'    => 'Reports',
            'sort_order'  => 511,
            'is_active'   => true,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    public function down(): void
    {
        $module = DB::table('modules')->where('slug', 'report-hell-cats-team')->first();

        if ($module) {
            DB::table('role_module_permissions')->where('module_id', $module->id)->delete();
            DB::table('modules')->where('id', $module->id)->delete();
        }
    }
};
