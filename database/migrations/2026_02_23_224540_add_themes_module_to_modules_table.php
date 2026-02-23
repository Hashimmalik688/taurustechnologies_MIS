<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('modules')->updateOrInsert(
            ['slug' => 'themes'],
            [
                'name' => 'Themes',
                'slug' => 'themes',
                'description' => 'Manage application theme and appearance settings',
                'category' => 'Settings',
                'sort_order' => 25,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('modules')->where('slug', 'themes')->delete();
    }
};
