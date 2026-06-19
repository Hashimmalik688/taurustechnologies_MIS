<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('modules')->insertOrIgnore([
            'name'        => 'Peregrine Performance Report',
            'slug'        => 'report-peregrine-performance',
            'description' => 'Per-agent Peregrine performance — Approved, Paid, Draft, Not Issued, Declined breakdown with leaderboard',
            'category'    => 'Reports',
            'sort_order'  => 510,
            'is_active'   => true,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('modules')->where('slug', 'report-peregrine-performance')->delete();
    }
};
