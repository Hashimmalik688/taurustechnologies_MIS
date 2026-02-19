<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\Roles;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = env('SUPER_ADMIN_EMAIL', 'admin@taurus.com');
        $user = User::where('email', $email)->first();
        if ($user) {
            // Don't overwrite password if user already exists — manage via User Management UI
            $this->command->info('Super Admin user already exists. Skipping password reset.');
        } else {
            $password = env('SUPER_ADMIN_PASSWORD', 'ChangeMe!123');
            $user = new User;
            $user->name = 'Super Admin';
            $user->email = $email;
            $user->password = bcrypt($password);
            $user->save();
            $user->assignRole(Roles::SUPER_ADMIN);
            $this->command->info('Super Admin user created. Change password via User Management.');
        }
    }
}
