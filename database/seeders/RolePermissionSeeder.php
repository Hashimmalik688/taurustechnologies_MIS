<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Arr;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Define permissions
        $permissions = [
            'manage_users',
            'create_users',
            'edit_users',
            'delete_users',
            'manage_payroll',
            'view_reports',
            'manage_settings',
            'manage_leads',
            'edit_leads',
            'delete_leads',
            'import_leads',
            'export_reports',
            'view_audit_logs',
            'manage_file_uploads',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Create roles
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $manager = Role::firstOrCreate(['name' => 'Manager']);
        $employee = Role::firstOrCreate(['name' => 'Employee']);
        $agent = Role::firstOrCreate(['name' => 'Agent']);

        // Assign permissions
        $allPermissionNames = Permission::pluck('name')->toArray();
        $superAdmin->syncPermissions($allPermissionNames);

        // Manager: most view + edit but not delete critical settings
        $managerPerms = Arr::only(array_flip($allPermissionNames), ['manage_users','view_reports','manage_leads','edit_leads','export_reports']);
        // fallback: give a subset
        $manager->syncPermissions(['view_reports','manage_leads','edit_leads','export_reports']);

        // Employee: limited permissions
        $employee->syncPermissions(['manage_leads','edit_leads','import_leads']);

        // Agent: very limited
        $agent->syncPermissions(['manage_leads']);

        // Assign Super Admin to first user if present
        $firstUser = User::orderBy('id')->first();
        if ($firstUser) {
            $firstUser->assignRole('Super Admin');
        }
    }
}
