<?php

namespace Database\Seeders;

use App\Models\Lead;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DummyLeadsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Dummy seeder disabled — removed per cleanup request.
        $this->command->info('DummyLeadsSeeder disabled.');
    }
}