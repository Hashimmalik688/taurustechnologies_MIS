<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\InsuranceCarrier;

class InsuranceCarrierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $carriers = [
            [
                'name' => 'American Amicable',
                'base_commission_percentage' => 85.00,
                'age_min' => 18,
                'age_max' => 80,
                'plan_types' => ['Term', 'Whole Life', 'Universal'],
                'calculation_notes' => 'Standard 85% commission. Age 65+ may have reduced rates.',
                'is_active' => true,
            ],
            [
                'name' => 'Foresters',
                'base_commission_percentage' => 90.00,
                'age_min' => 0,
                'age_max' => 80,
                'plan_types' => ['Term', 'Whole Life'],
                'calculation_notes' => '90% for ages 0-65, 80% for ages 66-80.',
                'is_active' => true,
            ],
            [
                'name' => 'Mutual of Omaha',
                'base_commission_percentage' => 80.00,
                'age_min' => 18,
                'age_max' => 85,
                'plan_types' => ['Term', 'Whole Life', 'Universal', 'Final Expense'],
                'calculation_notes' => 'Varies by product: Term 80%, Whole Life 90%, Final Expense 100%.',
                'is_active' => true,
            ],
            [
                'name' => 'Globe Life',
                'base_commission_percentage' => 100.00,
                'age_min' => 0,
                'age_max' => 90,
                'plan_types' => ['Term', 'Final Expense'],
                'calculation_notes' => '100% first year, 10% renewal. No medical exam required.',
                'is_active' => true,
            ],
            [
                'name' => 'Lincoln Heritage',
                'base_commission_percentage' => 95.00,
                'age_min' => 40,
                'age_max' => 85,
                'plan_types' => ['Final Expense', 'Burial Insurance'],
                'calculation_notes' => '95% commission for ages 40-75, 85% for 76-85.',
                'is_active' => true,
            ],
            [
                'name' => 'Transamerica',
                'base_commission_percentage' => 75.00,
                'age_min' => 18,
                'age_max' => 70,
                'plan_types' => ['Term', 'Whole Life', 'Universal'],
                'calculation_notes' => 'Lower commission but higher customer retention. Premium products.',
                'is_active' => true,
            ],
        ];

        foreach ($carriers as $carrier) {
            InsuranceCarrier::create($carrier);
        }
    }
}
