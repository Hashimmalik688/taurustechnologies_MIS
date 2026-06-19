<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('modules')->insertOrIgnore([
            'name'        => 'Ravens Performance Report',
            'slug'        => 'report-ravens-performance',
            'description' => 'Per-agent Ravens performance — Approved, Paid, Draft, Not Issued, Declined breakdown with leaderboard',
            'category'    => 'Reports',
            'sort_order'  => 511,
            'is_active'   => true,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('modules')->where('slug', 'report-ravens-performance')->delete();
    }
};
