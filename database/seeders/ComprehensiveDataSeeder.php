<?php

namespace Database\Seeders;

use App\Models\Lead;
use App\Models\Carrier;
use App\Models\LedgerEntry;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ComprehensiveDataSeeder extends Seeder
{
    private $closers = ['Mike Johnson', 'Sarah Chen', 'David Kim', 'Jessica Lopez', 'Robert Martinez'];
    private $sources = ['Facebook Ads', 'Google Ads', 'Referral', 'Cold Call', 'Website', 'Email Campaign'];
    private $carriers = [
        'Mutual of Omaha', 'State Farm', 'Prudential', 'MetLife', 'New York Life',
        'Transamerica', 'AIG', 'Principal Financial', 'Lincoln Financial', 'Nationwide',
        'Banner Life', 'Pacific Life', 'American General', 'Protective Life'
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating comprehensive dummy data...');

        // Create realistic leads with ALL fields populated
        $leads = $this->createLeads();

        // Create carriers for each lead
        $this->createCarriers($leads);

        // Create chargebacks (ledger entries)
        $this->createChargebacks($leads);

        $this->command->info('✅ Comprehensive dummy data created successfully!');
    }

    private function createLeads()
    {
        $leadsData = [
            [
                'cn_name' => 'John Michael Anderson',
                'phone_number' => '5551234567',
                'date_of_birth' => '1985-03-15',
                'gender' => 'Male',
                'smoker' => 0,
                'driving_license' => 'DL-IL-85031544',
                'height_weight' => '6\'0" / 185 lbs',
                'birth_place' => 'Springfield, Illinois',
                'medical_issue' => 'High blood pressure, controlled with medication',
                'medications' => 'Lisinopril 10mg daily, Atorvastatin 20mg daily',
                'doctor_name' => 'Dr. Sarah Mitchell',
                'ssn' => '123-45-6789',
                'address' => '123 Main Street, Apt 4B, Springfield, IL 62701',
                'carrier_name' => 'Mutual of Omaha',
                'coverage_amount' => 250000,
                'monthly_premium' => 125.50,
                'beneficiary' => 'Sarah Elizabeth Anderson (Spouse)',
                'beneficiary_dob' => '1987-06-20',
                'emergency_contact' => 'Sarah Anderson - (555) 234-5678',
                'policy_type' => 'Term Life - 20 Year',
                'initial_draft_date' => Carbon::now()->addDays(15)->format('Y-m-d'),
                'future_draft_date' => Carbon::now()->addMonths(1)->addDays(15)->format('Y-m-d'),
                'bank_name' => 'Chase Bank',
                'account_type' => 'Checking',
                'routing_number' => '021000021',
                'acc_number' => '****5678',
                'account_verified_by' => 'Bank Statement - verified 2025-01-15',
                'bank_balance' => 8500.00,
                'card_number' => '4532********1234',
                'source' => 'Facebook Ads',
                'closer_name' => 'Mike Johnson',
                'preset_line' => 'Line 1 - Main Sales',
                'comments' => 'Customer very interested, prefers email communication. Follow up on 15th.',
                'status' => 'accepted',
                'date' => Carbon::now()->subDays(2)->format('Y-m-d'),
                'created_at' => Carbon::now()->subDays(2),
            ],
            [
                'cn_name' => 'Emily Rose Rodriguez',
                'phone_number' => '5559876543',
                'date_of_birth' => '1990-07-22',
                'gender' => 'Female',
                'smoker' => 0,
                'driving_license' => 'DL-TX-90072255',
                'height_weight' => '5\'5" / 135 lbs',
                'birth_place' => 'Austin, Texas',
                'medical_issue' => 'Asthma - well controlled',
                'medications' => 'Albuterol inhaler as needed, Advair Diskus 250/50 twice daily',
                'doctor_name' => 'Dr. James Chen',
                'ssn' => '234-56-7890',
                'address' => '456 Oak Avenue, Building C, Unit 12, Austin, TX 73301',
                'carrier_name' => 'State Farm',
                'coverage_amount' => 500000,
                'monthly_premium' => 245.75,
                'beneficiary' => 'Carlos Rodriguez (Father)',
                'beneficiary_dob' => '1965-03-10',
                'emergency_contact' => 'Maria Rodriguez - (555) 876-5432',
                'policy_type' => 'Whole Life',
                'initial_draft_date' => Carbon::now()->addDays(10)->format('Y-m-d'),
                'future_draft_date' => Carbon::now()->addMonths(1)->addDays(10)->format('Y-m-d'),
                'bank_name' => 'Wells Fargo',
                'account_type' => 'Savings',
                'routing_number' => '121000248',
                'acc_number' => '****9012',
                'account_verified_by' => 'Check Book Photo - verified 2025-01-18',
                'bank_balance' => 12300.00,
                'card_number' => '5425********6789',
                'source' => 'Google Ads',
                'closer_name' => 'Sarah Chen',
                'preset_line' => 'Line 2 - Premium Sales',
                'comments' => 'Excellent credit score. Requested Spanish-speaking agent for parents.',
                'status' => 'accepted',
                'date' => Carbon::now()->subDays(5)->format('Y-m-d'),
                'created_at' => Carbon::now()->subDays(5),
            ],
            [
                'cn_name' => 'Michael Robert Thompson',
                'phone_number' => '5552345678',
                'date_of_birth' => '1978-11-30',
                'gender' => 'Male',
                'smoker' => 1,
                'driving_license' => 'DL-WA-78113066',
                'height_weight' => '5\'11" / 195 lbs',
                'birth_place' => 'Seattle, Washington',
                'medical_issue' => 'Type 2 Diabetes, smoker (trying to quit)',
                'medications' => 'Metformin 1000mg twice daily, Nicotine patches 21mg',
                'doctor_name' => 'Dr. Robert Williams',
                'ssn' => '345-67-8901',
                'address' => '789 Pine Road, Suite 301, Seattle, WA 98101',
                'carrier_name' => 'Prudential',
                'coverage_amount' => 1000000,
                'monthly_premium' => 485.00,
                'beneficiary' => 'Jennifer Lynn Thompson (Spouse)',
                'beneficiary_dob' => '1980-09-15',
                'emergency_contact' => 'Jennifer Thompson - (555) 345-6789',
                'policy_type' => 'Term Life - 30 Year',
                'initial_draft_date' => Carbon::now()->addDays(20)->format('Y-m-d'),
                'future_draft_date' => Carbon::now()->addMonths(1)->addDays(20)->format('Y-m-d'),
                'bank_name' => 'Bank of America',
                'account_type' => 'Checking',
                'routing_number' => '026009593',
                'acc_number' => '****3456',
                'account_verified_by' => 'Bank Statement - verified 2025-01-20',
                'bank_balance' => 15750.00,
                'card_number' => '4916********2345',
                'source' => 'Referral',
                'closer_name' => 'David Kim',
                'preset_line' => 'Line 1 - Main Sales',
                'comments' => 'High premium due to smoking. Medical exam scheduled for next week.',
                'status' => 'underwritten',
                'date' => Carbon::now()->subDays(1)->format('Y-m-d'),
                'created_at' => Carbon::now()->subDays(1),
            ],
            [
                'cn_name' => 'Jessica Marie Martinez',
                'phone_number' => '5553456789',
                'date_of_birth' => '1988-05-18',
                'gender' => 'Female',
                'smoker' => 0,
                'driving_license' => 'DL-FL-88051877',
                'height_weight' => '5\'6" / 145 lbs',
                'birth_place' => 'Miami, Florida',
                'medical_issue' => 'None - excellent health',
                'medications' => 'Multivitamin daily',
                'doctor_name' => 'Dr. Amanda Lopez',
                'ssn' => '456-78-9012',
                'address' => '321 Elm Street, Floor 2, Miami, FL 33101',
                'carrier_name' => 'MetLife',
                'coverage_amount' => 750000,
                'monthly_premium' => 365.25,
                'beneficiary' => 'Robert Martinez (Brother)',
                'beneficiary_dob' => '1985-12-05',
                'emergency_contact' => 'Maria Martinez (Mother) - (555) 456-7890',
                'policy_type' => 'Universal Life',
                'initial_draft_date' => Carbon::now()->addDays(5)->format('Y-m-d'),
                'future_draft_date' => Carbon::now()->addMonths(1)->addDays(5)->format('Y-m-d'),
                'bank_name' => 'TD Bank',
                'account_type' => 'Checking',
                'routing_number' => '031101279',
                'acc_number' => '****7890',
                'account_verified_by' => 'Online Banking Screenshot - verified 2025-01-19',
                'bank_balance' => 9800.00,
                'card_number' => '3782********3456',
                'source' => 'Website',
                'closer_name' => 'Jessica Lopez',
                'preset_line' => 'Line 3 - Web Leads',
                'comments' => 'Self-employed. Provided tax returns for income verification.',
                'status' => 'accepted',
                'date' => Carbon::now()->subDays(7)->format('Y-m-d'),
                'created_at' => Carbon::now()->subDays(7),
            ],
            [
                'cn_name' => 'Robert James Williams',
                'phone_number' => '5554567890',
                'date_of_birth' => '1982-09-10',
                'gender' => 'Male',
                'smoker' => 0,
                'driving_license' => 'DL-MA-82091033',
                'height_weight' => '6\'2" / 210 lbs',
                'birth_place' => 'Boston, Massachusetts',
                'medical_issue' => 'Previous knee surgery (2020), fully recovered',
                'medications' => 'Fish oil supplement',
                'doctor_name' => 'Dr. Michael Brown',
                'ssn' => '567-89-0123',
                'address' => '654 Maple Drive, Boston, MA 02101',
                'carrier_name' => 'New York Life',
                'coverage_amount' => 300000,
                'monthly_premium' => 155.00,
                'beneficiary' => 'Linda Williams (Spouse)',
                'beneficiary_dob' => '1984-07-25',
                'emergency_contact' => 'Linda Williams - (555) 567-8901',
                'policy_type' => 'Term Life - 15 Year',
                'initial_draft_date' => Carbon::now()->addDays(8)->format('Y-m-d'),
                'future_draft_date' => Carbon::now()->addMonths(1)->addDays(8)->format('Y-m-d'),
                'bank_name' => 'Citibank',
                'account_type' => 'Checking',
                'routing_number' => '021000089',
                'acc_number' => '****2345',
                'account_verified_by' => 'Voided Check - verified 2025-01-17',
                'bank_balance' => 6750.00,
                'card_number' => '6011********4567',
                'source' => 'Cold Call',
                'closer_name' => 'Robert Martinez',
                'preset_line' => 'Line 4 - Outbound',
                'comments' => 'Initially hesitant but became interested after coverage explanation. Needs to discuss with spouse.',
                'status' => 'rejected',
                'date' => Carbon::now()->subDays(10)->format('Y-m-d'),
                'created_at' => Carbon::now()->subDays(10),
            ],
            // Chargeback cases
            [
                'cn_name' => 'Amanda Grace Davis',
                'phone_number' => '5556789012',
                'date_of_birth' => '1995-02-14',
                'gender' => 'Female',
                'smoker' => 0,
                'driving_license' => 'DL-CO-95021488',
                'height_weight' => '5\'4" / 125 lbs',
                'birth_place' => 'Denver, Colorado',
                'medical_issue' => 'Seasonal allergies',
                'medications' => 'Claritin 10mg as needed',
                'doctor_name' => 'Dr. Patricia Johnson',
                'ssn' => '678-90-1234',
                'address' => '987 Cedar Lane, Denver, CO 80201',
                'carrier_name' => 'Transamerica',
                'coverage_amount' => 400000,
                'monthly_premium' => 195.50,
                'beneficiary' => 'Mark Davis (Father)',
                'beneficiary_dob' => '1970-04-12',
                'emergency_contact' => 'Susan Davis (Mother) - (555) 678-9012',
                'policy_type' => 'Term Life - 20 Year',
                'initial_draft_date' => Carbon::now()->subDays(30)->format('Y-m-d'),
                'future_draft_date' => Carbon::now()->subDays(1)->format('Y-m-d'),
                'bank_name' => 'US Bank',
                'account_type' => 'Checking',
                'routing_number' => '091000019',
                'acc_number' => '****6789',
                'account_verified_by' => 'Bank Statement - verified 2024-12-20',
                'bank_balance' => 3200.00,
                'card_number' => '5105********7890',
                'source' => 'Email Campaign',
                'closer_name' => 'David Kim',
                'preset_line' => 'Line 2 - Premium Sales',
                'comments' => 'CHARGEBACK - Customer disputed first payment. Bank returned funds. Attempting retention.',
                'status' => 'chargeback',
                'date' => Carbon::now()->subDays(45)->format('Y-m-d'),
                'created_at' => Carbon::now()->subDays(45),
            ],
            [
                'cn_name' => 'Christopher Paul Brown',
                'phone_number' => '5557890123',
                'date_of_birth' => '1975-12-05',
                'gender' => 'Male',
                'smoker' => 1,
                'driving_license' => 'DL-AZ-75120522',
                'height_weight' => '5\'10" / 200 lbs',
                'birth_place' => 'Phoenix, Arizona',
                'medical_issue' => 'High cholesterol, smoker',
                'medications' => 'Lipitor 40mg daily',
                'doctor_name' => 'Dr. Steven Martinez',
                'ssn' => '789-01-2345',
                'address' => '147 Birch Street, Phoenix, AZ 85001',
                'carrier_name' => 'AIG',
                'coverage_amount' => 850000,
                'monthly_premium' => 425.75,
                'beneficiary' => 'Patricia Brown (Spouse)',
                'beneficiary_dob' => '1977-08-30',
                'emergency_contact' => 'Patricia Brown - (555) 789-0123',
                'policy_type' => 'Term Life - 25 Year',
                'initial_draft_date' => Carbon::now()->subDays(60)->format('Y-m-d'),
                'future_draft_date' => Carbon::now()->subDays(30)->format('Y-m-d'),
                'bank_name' => 'PNC Bank',
                'account_type' => 'Checking',
                'routing_number' => '043000096',
                'acc_number' => '****8901',
                'account_verified_by' => 'Check Book Photo - verified 2024-11-15',
                'bank_balance' => 11200.00,
                'card_number' => '4539********9012',
                'source' => 'Facebook Ads',
                'closer_name' => 'Mike Johnson',
                'preset_line' => 'Line 1 - Main Sales',
                'comments' => 'CHARGEBACK - NSF on second payment. Account closed by customer. Retention attempt failed.',
                'status' => 'chargeback',
                'date' => Carbon::now()->subDays(90)->format('Y-m-d'),
                'created_at' => Carbon::now()->subDays(90),
            ],
        ];

        $leads = [];
        foreach ($leadsData as $data) {
            $leads[] = Lead::create($data);
        }

        $this->command->info('✓ Created ' . count($leads) . ' leads with complete information');

        return $leads;
    }

    private function createCarriers($leads)
    {
        foreach ($leads as $lead) {
            // Create primary carrier
            Carrier::create([
                'lead_id' => $lead->id,
                'name' => $lead->carrier_name,
                'policy_number' => 'POL-' . strtoupper(substr($lead->carrier_name, 0, 3)) . '-' . rand(100000, 999999),
                'coverage_amount' => $lead->coverage_amount,
                'premium_amount' => $lead->monthly_premium,
                'status' => $lead->status,
                'phone' => '1-800-' . rand(100, 999) . '-' . rand(1000, 9999),
                'email' => strtolower(str_replace(' ', '', $lead->carrier_name)) . '@insurance.com',
                'notes' => 'Primary policy for ' . $lead->cn_name,
                'created_at' => $lead->created_at,
            ]);

            // 30% chance of having a secondary carrier
            if (rand(1, 100) <= 30) {
                $secondaryCarrier = $this->carriers[array_rand($this->carriers)];
                Carrier::create([
                    'lead_id' => $lead->id,
                    'name' => $secondaryCarrier,
                    'policy_number' => 'POL-' . strtoupper(substr($secondaryCarrier, 0, 3)) . '-' . rand(100000, 999999),
                    'coverage_amount' => rand(100000, 500000),
                    'premium_amount' => rand(50, 200),
                    'status' => 'accepted',
                    'phone' => '1-800-' . rand(100, 999) . '-' . rand(1000, 9999),
                    'email' => strtolower(str_replace(' ', '', $secondaryCarrier)) . '@insurance.com',
                    'notes' => 'Secondary/supplemental policy',
                    'created_at' => $lead->created_at->addDays(rand(1, 10)),
                ]);
            }
        }

        $this->command->info('✓ Created carriers with policy numbers and contact information');
    }

    private function createChargebacks($leads)
    {
        // Chargebacks are already marked in the leads table with status='chargeback'
        // The chargeback page will display these leads
        $chargebackCount = collect($leads)->where('status', 'chargeback')->count();
        $this->command->info("✓ Created {$chargebackCount} chargeback cases (marked in leads)");
    }
}
