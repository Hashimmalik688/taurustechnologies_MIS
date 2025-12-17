<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = new User;
        $user->name = 'Super Admin';
        $user->email = 'admin@taurus.com';
        $user->password = bcrypt('password');
        $user->save();
        $user->assignRole('Super Admin');
    }
}
