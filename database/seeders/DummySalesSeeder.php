<?php

namespace Database\Seeders;

use App\Models\Lead;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DummySalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get dummy data arrays
        $closers = ['John Smith', 'Sarah Johnson', 'Mike Davis', 'Emily Brown', 'David Wilson'];
        $carriers = ['GW', 'OTL', 'Americo', 'Mutual of Omaha', 'Transamerica'];
        $coverages = [5000, 10000, 15000, 20000, 25000, 50000, 100000];
        $premiums = [40.50, 57.45, 75.00, 96.63, 125.00, 150.00, 200.00];

        // Create 10 dummy sales
        for ($i = 1; $i <= 10; $i++) {
            Lead::create([
                'cn_name' => 'Test Customer ' . $i,
                'phone_number' => '555010' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'date_of_birth' => Carbon::now()->subYears(rand(30, 70))->format('Y-m-d'),
                'closer_name' => $closers[array_rand($closers)],
                'carrier_name' => $carriers[array_rand($carriers)],
                'coverage_amount' => $coverages[array_rand($coverages)],
                'monthly_premium' => $premiums[array_rand($premiums)],
                'policy_type' => ['Term', 'Whole Life', 'Universal'][array_rand(['Term', 'Whole Life', 'Universal'])],
                'status' => 'pending',
                'sale_at' => Carbon::now()->subDays(rand(0, 30)),
                'sale_date' => Carbon::now()->subDays(rand(0, 30))->format('Y-m-d'),
                'comments' => 'Dummy sale for testing QA and Sales flow',
            ]);
        }

        $this->command->info('Created 10 dummy sales successfully!');
    }
}
