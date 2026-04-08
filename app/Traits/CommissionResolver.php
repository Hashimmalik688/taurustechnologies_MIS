<?php

namespace App\Traits;

use App\Models\Lead;
use App\Services\CommissionCalculationService;

/**
 * Shared commission-resolution logic used by any controller that needs to
 * calculate a lead's commission for ledger posting.
 *
 * Traits the methods from PaidSalesController so they are available in
 * PendingDraftController and any other controller that needs them.
 */
trait CommissionResolver
{
    /**
     * Resolve the policy type string from a lead's settlement_type / policy_type.
     * Returns one of: 'gi', 'graded', 'modified', 'level'.
     */
    protected function resolveCommissionType(Lead $lead): string
    {
        $raw = strtolower(trim($lead->settlement_type ?: $lead->policy_type ?: ''));
        if (str_contains($raw, 'g.i') || str_contains($raw, 'gi')) return 'gi';
        if (str_contains($raw, 'grad'))                             return 'graded';
        if (str_contains($raw, 'modif'))                            return 'modified';
        return 'level';
    }

    /**
     * Calculate commission totals for a lead.
     * Returns ['commission' => float, 'our_share' => float, 'our_share_pct' => float].
     */
    protected function calcLeadCommission(Lead $lead, CommissionCalculationService $commSvc): array
    {
        $result = $commSvc->calculateCommission(
            $lead->partner_id ?? 0,
            $lead->insurance_carrier_id ?? 0,
            $lead->state ?? '',
            $this->resolveCommissionType($lead),
            (float) ($lead->monthly_premium ?? 0)
        );
        $commission  = $result['success'] ? (float) ($result['commission'] ?? 0) : 0;
        $ourSharePct = $lead->partner ? (float) ($lead->partner->our_commission_percentage ?? 15.0) : 15.0;

        return [
            'commission'    => round($commission, 2),
            'our_share'     => round($commission * ($ourSharePct / 100), 2),
            'our_share_pct' => $ourSharePct,
        ];
    }
}
