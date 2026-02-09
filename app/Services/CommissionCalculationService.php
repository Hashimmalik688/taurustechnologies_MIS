<?php

namespace App\Services;

use App\Models\AgentCarrierState;
use App\Models\AgentCarrierCommission;
use App\Models\InsuranceCarrier;

class CommissionCalculationService
{
    /**
     * Calculate commission based on partner, carrier, state, settlement type and monthly premium.
     * Formula: Monthly Premium × 9 months × Settlement %
     *
     * @param int $partnerId
     * @param int $carrierId
     * @param string $state
     * @param string $settlementType ('level', 'graded', 'gi', 'modified')
     * @param float $monthlyPremium
     * @return array ['success' => bool, 'commission' => float|null, 'message' => string, 'settlement_pct' => float|null]
     */
    public function calculateCommission(
        int $partnerId,
        int $carrierId,
        string $state,
        string $settlementType,
        float $monthlyPremium
    ): array {
        // Validate settlement type
        if (!in_array($settlementType, ['level', 'graded', 'gi', 'modified'])) {
            return [
                'success' => false,
                'commission' => null,
                'message' => 'Invalid settlement type',
                'settlement_pct' => null,
            ];
        }

        // Try to find partner-carrier-state record
        $agentCarrierState = AgentCarrierState::where('partner_id', $partnerId)
            ->where('insurance_carrier_id', $carrierId)
            ->where('state', $state)
            ->first();

        if ($agentCarrierState) {
            $settlementPct = $agentCarrierState->getSettlementPercentage($settlementType);
            
            if ($settlementPct !== null && $settlementPct > 0) {
                $commission = $monthlyPremium * 9 * ($settlementPct / 100);
                
                return [
                    'success' => true,
                    'commission' => round($commission, 2),
                    'message' => "Commission calculated using state-specific {$settlementType} rate ({$settlementPct}%)",
                    'settlement_pct' => $settlementPct,
                ];
            }
        }

        // Fallback: Try partner-carrier base commission
        $agentCarrierCommission = AgentCarrierCommission::where('partner_id', $partnerId)
            ->where('insurance_carrier_id', $carrierId)
            ->first();

        if ($agentCarrierCommission && $agentCarrierCommission->commission_percentage > 0) {
            $commission = $monthlyPremium * 9 * ($agentCarrierCommission->commission_percentage / 100);
            
            return [
                'success' => true,
                'commission' => round($commission, 2),
                'message' => "Commission calculated using partner-carrier base rate ({$agentCarrierCommission->commission_percentage}%) - No state-specific rate found",
                'settlement_pct' => $agentCarrierCommission->commission_percentage,
            ];
        }

        // Fallback: Try carrier base commission
        $carrier = InsuranceCarrier::find($carrierId);
        if ($carrier && $carrier->base_commission_percentage > 0) {
            $commission = $monthlyPremium * 9 * ($carrier->base_commission_percentage / 100);
            
            return [
                'success' => true,
                'commission' => round($commission, 2),
                'message' => "Commission calculated using carrier base rate ({$carrier->base_commission_percentage}%) - No partner-specific rate found",
                'settlement_pct' => $carrier->base_commission_percentage,
            ];
        }

        // No commission rate found
        return [
            'success' => false,
            'commission' => null,
            'message' => "No commission rate configured for this partner-carrier-state-settlement combination",
            'settlement_pct' => null,
        ];
    }

    /**
     * Validate if partner can sell in this state for this carrier.
     *
     * @param int $partnerId
     * @param int $carrierId
     * @param string $state
     * @return bool
     */
    public function canPartnerSellInState(int $partnerId, int $carrierId, string $state): bool
    {
        return AgentCarrierState::where('partner_id', $partnerId)
            ->where('insurance_carrier_id', $carrierId)
            ->where('state', $state)
            ->exists();
    }

    /**
     * Get all states where partner can sell for a carrier.
     *
     * @param int $partnerId
     * @param int $carrierId
     * @return array
     */
    public function getPartnerStatesForCarrier(int $partnerId, int $carrierId): array
    {
        return AgentCarrierState::where('partner_id', $partnerId)
            ->where('insurance_carrier_id', $carrierId)
            ->pluck('state')
            ->toArray();
    }

    // Legacy methods for backwards compatibility (deprecated)
    
    /**
     * @deprecated Use calculateCommission with partnerId instead
     */
    public function calculateCommissionLegacy(
        int $agentId,
        int $carrierId,
        string $state,
        string $settlementType,
        float $monthlyPremium
    ): array {
        // Legacy support - try to find partner by user_id
        $agentCarrierState = AgentCarrierState::where('user_id', $agentId)
            ->where('insurance_carrier_id', $carrierId)
            ->where('state', $state)
            ->first();

        if ($agentCarrierState && $agentCarrierState->partner_id) {
            return $this->calculateCommission(
                $agentCarrierState->partner_id,
                $carrierId,
                $state,
                $settlementType,
                $monthlyPremium
            );
        }

        return [
            'success' => false,
            'commission' => null,
            'message' => 'Agent not linked to partner. Please use partner-based commission calculation.',
            'settlement_pct' => null,
        ];
    }
}
