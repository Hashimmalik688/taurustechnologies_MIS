<?php
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

return new class extends Migration {
    public function up(): void
    {
        // Create CEO role if it doesn't exist
        if (!Role::where('name', 'CEO')->exists()) {
            Role::create(['name' => 'CEO', 'guard_name' => 'web']);
        }
    }

    public function down(): void
    {
        // Remove CEO role
        Role::where('name', 'CEO')->delete();
    }
};
