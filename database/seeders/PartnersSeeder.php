<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\InsuranceCarrier;
use App\Models\AgentCarrierState;
use App\Models\AgentCarrierCommission;
use App\Models\Partner;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class PartnersSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Temporarily disable User observer
        User::unsetEventDispatcher();

        // Ensure Agent role exists
        $agentRole = Role::firstOrCreate(['name' => 'Agent']);

        // Get insurance carriers
        $carriers = [
            'AIG' => InsuranceCarrier::where('name', 'AIG')->first(),
            'TransAmerica' => InsuranceCarrier::where('name', 'Transamerica')->first(),
            'Securico' => InsuranceCarrier::where('name', 'Securico')->first(),
        ];

        if (! $carriers['AIG'] || ! $carriers['TransAmerica'] || ! $carriers['Securico']) {
            $this->command->error('❌ Required carriers (AIG, Transamerica, Securico) not found. Please create them first.');
            return;
        }

        // Partner E-1
        $e1User = $this->createPartnerUser('E-1', 'e1@taurustechnologies.co', 'pass123');
        $e1Partner = Partner::updateOrCreate(['code' => 'E-1'], ['name' => 'E-1', 'email' => 'e1@taurustechnologies.co', 'is_active' => true]);
        
        // E-1 with AIG
        $this->createAgentCarrierStates($e1User->id, $e1Partner->id, $carriers['AIG']->id, [
            'states' => ['AR', 'FL', 'GA', 'IN', 'KS', 'LA', 'MD', 'MI', 'MN', 'NM', 'NC', 'OH', 'SC', 'TN', 'TX', 'VA', 'WV', 'WI'],
            'level' => 95.00,
            'graded' => 75.00,
            'gi' => 60.00,
            'modified' => null,
        ]);

        // Partner Y-1
        $y1User = $this->createPartnerUser('Y-1', 'y1@taurustechnologies.co', 'pass123');
        $y1Partner = Partner::updateOrCreate(['code' => 'Y-1'], ['name' => 'Y-1', 'email' => 'y1@taurustechnologies.co', 'is_active' => true]);
        
        // Y-1 with AIG
        $this->createAgentCarrierStates($y1User->id, $y1Partner->id, $carriers['AIG']->id, [
            'states' => ['AR', 'CO', 'CT', 'DE', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MD', 'MI', 'MN', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ', 'NC', 'ND', 'OH', 'OK', 'OR', 'PA', 'RI', 'SC', 'SD', 'TN', 'TX', 'WA', 'WY'],
            'level' => 95.00,
            'graded' => 75.00,
            'gi' => 60.00,
            'modified' => null,
        ]);

        // Y-1 with TransAmerica
        $this->createAgentCarrierStates($y1User->id, $y1Partner->id, $carriers['TransAmerica']->id, [
            'states' => ['AR', 'CO', 'CT', 'DE', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MD', 'MI', 'MN', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ', 'NC', 'ND', 'OH', 'OK', 'OR', 'PA', 'RI', 'SC', 'SD', 'TN', 'TX', 'WA', 'WY'],
            'level' => 115.00,
            'graded' => 85.00,
            'gi' => null,
            'modified' => null,
        ]);

        // Partner F-1
        $f1User = $this->createPartnerUser('F-1', 'f1@taurustechnologies.co', 'pass123');
        $f1Partner = Partner::updateOrCreate(['code' => 'F-1'], ['name' => 'F-1', 'email' => 'f1@taurustechnologies.co', 'is_active' => true]);
        
        // F-1 with Securico
        $this->createAgentCarrierStates($f1User->id, $f1Partner->id, $carriers['Securico']->id, [
            'states' => ['AL', 'AZ', 'CO', 'CT', 'DC', 'IL', 'IN', 'KY', 'ME', 'MD', 'MA', 'MI', 'MN', 'MS', 'MO', 'NV', 'NC', 'OH', 'SC', 'TX', 'VT', 'VA', 'WA', 'WI'],
            'level' => 115.00,
            'graded' => 90.00,
            'gi' => 70.00,
            'modified' => null,
        ]);

        // F-1 with TransAmerica
        $this->createAgentCarrierStates($f1User->id, $f1Partner->id, $carriers['TransAmerica']->id, [
            'states' => ['DC', 'FL', 'GA', 'IN', 'KS', 'LA', 'MD', 'NC', 'OH', 'TX', 'WA', 'MO', 'SC'],
            'level' => 105.00,
            'graded' => 80.00,
            'gi' => null,
            'modified' => null,
        ]);

        $this->command->info('✅ Partners seeded successfully!');
        $this->command->info('   - E-1: AIG (18 states) - Level 95%, Graded 75%, GI 60%');
        $this->command->info('   - Y-1: AIG (34 states) - Level 95%, Graded 75%, GI 60%');
        $this->command->info('   - Y-1: TransAmerica (34 states) - Level 115%, Graded 85%');
        $this->command->info('   - F-1: Securico (24 states) - Level 115%, Graded 90%, GI 70%');
        $this->command->info('   - F-1: TransAmerica (13 states) - Level 105%, Graded 80%');
    }

    private function createPartnerUser($name, $email, $password)
    {
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]
        );

        if (!$user->hasRole('Agent')) {
            $user->assignRole('Agent');
        }

        UserDetail::updateOrCreate(
            ['user_id' => $user->id],
            [
                'phone' => '000-000-0000',
                'state' => 'Texas',
                'address' => 'Taurus Technologies',
                'active_states' => json_encode([]),
            ]
        );

        $this->command->info("Partner: {$name} ({$email})");
        return $user;
    }

    private function createAgentCarrierStates($userId, $partnerId, $carrierId, $config)
    {
        // Create agent_carrier_commission record
        AgentCarrierCommission::updateOrCreate(
            ['user_id' => $userId, 'insurance_carrier_id' => $carrierId],
            ['commission_percentage' => $config['level']]
        );

        // Create state-specific records
        foreach ($config['states'] as $state) {
            AgentCarrierState::updateOrCreate(
                [
                    'user_id' => $userId,
                    'partner_id' => $partnerId,
                    'insurance_carrier_id' => $carrierId,
                    'state' => $state,
                ],
                [
                    'settlement_level_pct' => $config['level'],
                    'settlement_graded_pct' => $config['graded'],
                    'settlement_gi_pct' => $config['gi'],
                    'settlement_modified_pct' => $config['modified'],
                ]
            );
        }
    }
}
