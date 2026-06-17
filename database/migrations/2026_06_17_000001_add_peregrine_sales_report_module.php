<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('modules')->insertOrIgnore([
            'name'        => 'Peregrine Sales Report',
            'slug'        => 'report-peregrine-sales',
            'description' => 'Individual Peregrine sales with pipeline status — pending contract, pending draft, paid, declined',
            'category'    => 'Reports',
            'sort_order'  => 509,
            'is_active'   => true,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('modules')->where('slug', 'report-peregrine-sales')->delete();
    }
};
