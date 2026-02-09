<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Lead;
use App\Models\InsuranceCarrier;
use App\Services\CommissionCalculationService;

class RecalculateCommissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leads:recalculate-commissions {--all : Recalculate for all leads even if already calculated}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate agent commissions for issued leads';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting commission recalculation...');
        
        $commissionService = new CommissionCalculationService();
        
        // Get issued leads with assigned agents and premium
        $query = Lead::where('issuance_status', 'Issued')
            ->whereNotNull('assigned_agent_id')
            ->where('monthly_premium', '>', 0);
        
        // If not --all flag, only recalculate leads without commission
        if (!$this->option('all')) {
            $query->whereNull('agent_commission');
        }
        
        $leads = $query->get();
        
        $this->info("Found {$leads->count()} leads to process");
        
        $bar = $this->output->createProgressBar($leads->count());
        $bar->start();
        
        $processed = 0;
        $failed = 0;
        $errors = [];
        
        foreach ($leads as $lead) {
            // Get carrier ID - lookup by name if not set
            $carrierId = $lead->insurance_carrier_id;
            if (!$carrierId && $lead->carrier_name) {
                $carrier = InsuranceCarrier::where('name', $lead->carrier_name)->first();
                if ($carrier) {
                    $carrierId = $carrier->id;
                    $lead->insurance_carrier_id = $carrierId;
                }
            }
            
            if ($carrierId) {
                // Get settlement type
                $policyType = $lead->settlement_type ?? $lead->policy_type ?? 'level';
                $settlementType = $this->getSettlementType($policyType);
                
                // Calculate commission
                $result = $commissionService->calculateCommission(
                    agentId: $lead->assigned_agent_id,
                    carrierId: $carrierId,
                    state: $lead->state ?? 'Unknown',
                    settlementType: $settlementType,
                    monthlyPremium: (float) $lead->monthly_premium
                );
                
                if ($result['success']) {
                    $lead->agent_commission = $result['commission'];
                    $lead->agent_revenue = $result['commission'];
                    $lead->settlement_percentage = $result['settlement_pct'];
                    $lead->commission_calculation_notes = $result['message'];
                    $lead->commission_calculated_at = now();
                    $lead->save();
                    $processed++;
                } else {
                    $failed++;
                    $errors[] = "Lead #{$lead->id} ({$lead->cn_name}): {$result['message']}";
                }
            } else {
                $failed++;
                $errors[] = "Lead #{$lead->id} ({$lead->cn_name}): Carrier '{$lead->carrier_name}' not found";
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine(2);
        
        $this->info("✓ Successfully processed: {$processed}");
        if ($failed > 0) {
            $this->warn("✗ Failed: {$failed}");
            if ($this->option('verbose')) {
                $this->newLine();
                $this->error('Errors:');
                foreach ($errors as $error) {
                    $this->line("  - {$error}");
                }
            }
        }
        
        return Command::SUCCESS;
    }
    
    private function getSettlementType($policyType)
    {
        $normalized = strtolower(trim($policyType));
        
        $mapping = [
            'g.i' => 'gi',
            'gi' => 'gi',
            'guaranteed issue' => 'gi',
            'graded' => 'graded',
            'level' => 'level',
            'modified' => 'modified',
            'term' => 'level',
            'whole life' => 'level',
            'universal' => 'level',
        ];
        
        return $mapping[$normalized] ?? 'level';
    }
}
