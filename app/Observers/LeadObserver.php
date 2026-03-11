<?php

namespace App\Observers;

use App\Models\Lead;
use App\Services\CommissionCalculationService;
use App\Support\Statuses;

class LeadObserver
{
    /**
     * Auto-recalculate agent_revenue when monthly_premium changes on an issued lead.
     * This ensures revenue analytics always reflect the latest premium values.
     */
    public function updating(Lead $lead): void
    {
        // Only recalculate if monthly_premium actually changed
        if (! $lead->isDirty('monthly_premium')) {
            return;
        }

        // Only for issued leads that have a partner assigned
        if ($lead->issuance_status !== Statuses::ISSUANCE_ISSUED) {
            return;
        }

        if (! $lead->partner_id) {
            return;
        }

        $newPremium = (float) $lead->monthly_premium;
        if ($newPremium <= 0) {
            return;
        }

        // Resolve carrier ID
        $carrierId = $lead->insurance_carrier_id;
        if (! $carrierId && $lead->carrier_name) {
            $carrier = \App\Models\InsuranceCarrier::where('name', $lead->carrier_name)->first();
            if ($carrier) {
                $carrierId = $carrier->id;
                $lead->insurance_carrier_id = $carrierId;
            }
        }

        if (! $carrierId) {
            return;
        }

        $commissionService = new CommissionCalculationService();

        // Determine settlement type
        $settlementType = $this->resolveSettlementType($lead->settlement_type ?? $lead->policy_type);

        $result = $commissionService->calculateCommission(
            partnerId: (int) $lead->partner_id,
            carrierId: (int) $carrierId,
            state: $lead->state ?? 'Unknown',
            settlementType: $settlementType,
            monthlyPremium: $newPremium
        );

        if ($result['success']) {
            $lead->agent_commission              = $result['commission'];
            $lead->agent_revenue                 = $result['commission'];
            $lead->settlement_percentage         = $result['settlement_pct'];
            $lead->commission_calculation_notes  = '[auto] ' . $result['message'];
            $lead->commission_calculated_at      = now();
        }
    }

    /**
     * Normalise policy type string to one of: level | graded | gi | modified
     */
    private function resolveSettlementType(?string $type): string
    {
        if (! $type) {
            return 'level';
        }

        $map = [
            'g.i'      => 'gi',
            'gi'       => 'gi',
            'graded'   => 'graded',
            'modified' => 'modified',
            'level'    => 'level',
        ];

        $normalised = strtolower(trim($type));
        return $map[$normalised] ?? 'level';
    }
}
