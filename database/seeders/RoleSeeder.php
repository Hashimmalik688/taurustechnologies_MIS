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
            'Peregrine Closer',
            'Peregrine Validator',
            'Retention Officer',
            'Ravens Closer',
            'Trainer',
            'Co-ordinator',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }
}
