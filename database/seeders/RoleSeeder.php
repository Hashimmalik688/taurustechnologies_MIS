<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'Super Admin',
            'Manager',
            'Employee',
            'Agent',
            'HR',
            'US Agent',
            'Vendor',
            'Verifier',
            'Live Closer',
            'QA Officer',
            'QA',
            'Sales Closer',
            'Closer',
            'Paraguins Closer',
            'Paraguins Validator',
            'Retention Officer',
            'Ravens Closer',
            'Trainer',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }
}
