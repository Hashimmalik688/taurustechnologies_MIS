<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class FixUserStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Set all users to active status
        User::whereIn('status', [null, 'inactive', 'suspended'])
            ->update(['status' => 'active']);

        echo "\n✅ All users set to active status for chat functionality\n";
    }
}
