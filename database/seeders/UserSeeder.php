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
        $user = User::where('email', 'admin@taurus.com')->first();
        if ($user) {
            $user->password = bcrypt('Hashim@431');
            $user->save();
        } else {
            $user = new User;
            $user->name = 'Super Admin';
            $user->email = 'admin@taurus.com';
            $user->password = bcrypt('Hashim@431');
            $user->save();
            $user->assignRole('Super Admin');
        }
    }
}
