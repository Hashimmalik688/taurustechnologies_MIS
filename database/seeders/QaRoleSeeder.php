<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class QaRoleSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create QA role if it doesn't exist
        $qaRole = Role::firstOrCreate(['name' => 'QA']);

        // Create QA specific permissions if they don't exist
        $permissions = [
            'view sales management',
            'update qa status',
            'view leads',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to QA role
        $qaRole->syncPermissions($permissions);

        $this->command->info('QA role created successfully with permissions.');
    }
}
