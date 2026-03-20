<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Module;

return new class extends Migration
{
    public function up(): void
    {
        Module::updateOrCreate(
            ['slug' => 'permission-manager'],
            [
                'name'        => 'Permission Manager',
                'slug'        => 'permission-manager',
                'description' => 'Manage role-based permissions for all modules',
                'category'    => 'Users Management',
                'sort_order'  => 295,
                'is_active'   => true,
            ]
        );
    }

    public function down(): void
    {
        Module::where('slug', 'permission-manager')->delete();
    }
};
