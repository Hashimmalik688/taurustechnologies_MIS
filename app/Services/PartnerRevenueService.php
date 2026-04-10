<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\Partner;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Partner Revenue & Performance Service
 * 
 * Calculates and aggregates partner financial metrics:
 * - Projected Revenue (from pending/issued contracts)
 * - Earned Revenue (from paid contracts)
 * - Chargebacks & adjustments
 * - Balance calculations
 * - Performance analytics by carrier/state
 */
class PartnerRevenueService
{
    protected Partner $partner;

    public function __construct(Partner $partner)
    {
        $this->partner = $partner;
    }

    /**
     * Get projected revenue (pending/issued, not yet paid)
     * Formula: premium × 9 × settlement % (from commission field)
     * 
     * @param \DateTime|null $from
     * @param \DateTime|null $to
     * @return float
     */
    public function getProjectedRevenue(\DateTime $from = null, \DateTime $to = null): float
    {
        $query = Lead::where('partner_id', $this->partner->id)
            ->where('issuance_status', 'Issued')
            ->whereNull('paid_at')
            ->where('monthly_premium', '>', 0);

        if ($from) {
            $query->where('issuance_date', '>=', $from);
        }
        if ($to) {
            $query->where('issuance_date', '<=', $to);
        }

        return $query->sum('agent_commission') ?? 0;
    }

    /**
     * Get earned revenue (paid/completed sales)
     * These are leads marked as paid_at by accounting
     * 
     * @param \DateTime|null $from
     * @param \DateTime|null $to
     * @return float
     */
    public function getEarnedRevenue(\DateTime $from = null, \DateTime $to = null): float
    {
        $query = Lead::where('partner_id', $this->partner->id)
            ->whereNotNull('paid_at')
            ->where('agent_commission', '>', 0);

        if ($from) {
            $query->where('paid_at', '>=', $from);
        }
        if ($to) {
            $query->where('paid_at', '<=', $to);
        }

        return $query->sum('agent_commission') ?? 0;
    }

    /**
     * Get total chargebacks (sales returns)
     * From ledger_journal_entries with type 'sales_return'
     * 
     * @param \DateTime|null $from
     * @param \DateTime|null $to
     * @return float
     */
    public function getTotalChargebacks(\DateTime $from = null, \DateTime $to = null): float
    {
        $query = DB::table('ledger_journal_entry_lines as l')
            ->join('ledger_journal_entries as je', 'l.journal_entry_id', '=', 'je.id')
            ->where('l.partner_id', $this->partner->id)
            ->whereIn('je.type', ['sales_return', 'chargeback']);

        if ($from) {
            $query->where('je.entry_date', '>=', $from);
        }
        if ($to) {
            $query->where('je.entry_date', '<=', $to);
        }

        // Sum credits for chargebacks (amounts owed back to partner)
        return $query->sum('l.credit') ?? 0;
    }

    /**
     * Get partner's balance from ledger (AR account 1200)
     * Running balance of what partner owes us
     * 
     * @return float Positive = partner owes us, Negative = we owe partner
     */
    public function getPartnerBalance(): float
    {
        $arAccount = DB::table('chart_of_accounts')
            ->where('account_code', '1200')
            ->first();

        if (!$arAccount) {
            return 0;
        }

        // Sum all debits (partner owes us) and credits (they paid or we owed them)
        $balance = DB::table('ledger_journal_entry_lines')
            ->where('partner_id', $this->partner->id)
            ->where('account_id', $arAccount->id)
            ->selectRaw('SUM(debit) - SUM(credit) as balance')
            ->pluck('balance')
            ->first() ?? 0;

        return (float) $balance;
    }

    /**
     * Get partner's earned commission share (after Taurus deduction)
     * Formula: earned_revenue - (earned_revenue × our_commission_percentage / 100)
     * 
     * @param \DateTime|null $from
     * @param \DateTime|null $to
     * @return float
     */
    public function getPartnerEarnedShare(\DateTime $from = null, \DateTime $to = null): float
    {
        $earnedRevenue = $this->getEarnedRevenue($from, $to);
        $ourSharePct = $this->partner->our_commission_percentage ?? 15.0;
        $ourShare = $earnedRevenue * ($ourSharePct / 100);

        return round($earnedRevenue - $ourShare, 2);
    }

    /**
     * Get partner's projected commission share (same formula)
     * 
     * @param \DateTime|null $from
     * @param \DateTime|null $to
     * @return float
     */
    public function getPartnerProjectedShare(\DateTime $from = null, \DateTime $to = null): float
    {
        $projectedRevenue = $this->getProjectedRevenue($from, $to);
        $ourSharePct = $this->partner->our_commission_percentage ?? 15.0;
        $ourShare = $projectedRevenue * ($ourSharePct / 100);

        return round($projectedRevenue - $ourShare, 2);
    }

    /**
     * Get breakdown of earned revenue by carrier
     * 
     * @param \DateTime|null $from
     * @param \DateTime|null $to
     * @return Collection
     */
    public function getEarnedRevenueByCarrier(\DateTime $from = null, \DateTime $to = null): Collection
    {
        $query = Lead::where('partner_id', $this->partner->id)
            ->whereNotNull('paid_at')
            ->with('insuranceCarrier')
            ->where('agent_commission', '>', 0);

        if ($from) {
            $query->where('paid_at', '>=', $from);
        }
        if ($to) {
            $query->where('paid_at', '<=', $to);
        }

        return $query->get()
            ->groupBy('insurance_carrier_id')
            ->map(function ($leads) {
                $total = $leads->sum('agent_commission');
                $ourSharePct = $this->partner->our_commission_percentage ?? 15.0;
                $ourShare = $total * ($ourSharePct / 100);
                $partnerShare = $total - $ourShare;

                return [
                    'carrier' => $leads->first()?->insuranceCarrier,
                    'carrier_id' => $leads->first()?->insurance_carrier_id,
                    'total_revenue' => round($total, 2),
                    'our_share' => round($ourShare, 2),
                    'partner_share' => round($partnerShare, 2),
                    'sales_count' => $leads->count(),
                ];
            })
            ->values();
    }

    /**
     * Get breakdown of earned revenue by state
     * 
     * @param \DateTime|null $from
     * @param \DateTime|null $to
     * @return Collection
     */
    public function getEarnedRevenueByState(\DateTime $from = null, \DateTime $to = null): Collection
    {
        $query = Lead::where('partner_id', $this->partner->id)
            ->whereNotNull('paid_at')
            ->where('agent_commission', '>', 0);

        if ($from) {
            $query->where('paid_at', '>=', $from);
        }
        if ($to) {
            $query->where('paid_at', '<=', $to);
        }

        return $query->get()
            ->groupBy('state')
            ->map(function ($leads) {
                $total = $leads->sum('agent_commission');
                $ourSharePct = $this->partner->our_commission_percentage ?? 15.0;
                $ourShare = $total * ($ourSharePct / 100);
                $partnerShare = $total - $ourShare;

                return [
                    'state' => $leads->first()?->state,
                    'total_revenue' => round($total, 2),
                    'our_share' => round($ourShare, 2),
                    'partner_share' => round($partnerShare, 2),
                    'sales_count' => $leads->count(),
                ];
            })
            ->sortByDesc('total_revenue')
            ->values();
    }

    /**
     * Get performance metrics: YTD summary
     * 
     * @param int $year
     * @return array
     */
    public function getYearToDateMetrics(int $year = null): array
    {
        $year = $year ?? now()->year;
        $from = \Carbon\Carbon::create($year, 1, 1)->startOfDay();
        $to = \Carbon\Carbon::create($year, 12, 31)->endOfDay();

        $projectedRevenue = $this->getProjectedRevenue($from, $to);
        $earnedRevenue = $this->getEarnedRevenue($from, $to);
        $chargebacks = $this->getTotalChargebacks($from, $to);
        $partnerEarned = $this->getPartnerEarnedShare($from, $to);

        return [
            'year' => $year,
            'projected_revenue' => round($projectedRevenue, 2),
            'earned_revenue' => round($earnedRevenue, 2),
            'chargebacks' => round($chargebacks, 2),
            'net_earned_revenue' => round($earnedRevenue - $chargebacks, 2),
            'partner_earned_share' => round($partnerEarned, 2),
            'taurus_share_pct' => $this->partner->our_commission_percentage ?? 15.0,
            'taurus_earned_share' => round($earnedRevenue - $partnerEarned, 2),
        ];
    }

    /**
     * Get monthly breakdown for dashboard timeline
     * 
     * @param int $year
     * @return Collection
     */
    public function getMonthlyBreakdown(int $year = null): Collection
    {
        $year = $year ?? now()->year;
        $months = [];

        for ($month = 1; $month <= 12; $month++) {
            $from = \Carbon\Carbon::create($year, $month, 1)->startOfDay();
            $to = $from->copy()->endOfMonth();

            $projected = $this->getProjectedRevenue($from, $to);
            $earned = $this->getEarnedRevenue($from, $to);
            $chargebacks = $this->getTotalChargebacks($from, $to);

            $months[] = [
                'month' => $month,
                'month_name' => $from->format('M'),
                'projected_revenue' => round($projected, 2),
                'earned_revenue' => round($earned, 2),
                'chargebacks' => round($chargebacks, 2),
                'net_revenue' => round($earned - $chargebacks, 2),
            ];
        }

        return collect($months);
    }

    /**
     * Get recent transactions (ledger entries for this partner)
     * Useful for balance reconciliation
     * 
     * @param int $limit
     * @return Collection
     */
    public function getRecentTransactions(int $limit = 50): Collection
    {
        return DB::table('ledger_journal_entry_lines as l')
            ->join('ledger_journal_entries as je', 'l.journal_entry_id', '=', 'je.id')
            ->leftJoin('insurance_carriers as ic', 'l.insurance_carrier_id', '=', 'ic.id')
            ->where('l.partner_id', $this->partner->id)
            ->orderByDesc('je.entry_date')
            ->orderByDesc('l.id')
            ->limit($limit)
            ->select([
                'je.entry_date',
                'je.type',
                'je.reference',
                'ic.name as carrier_name',
                'l.debit',
                'l.credit',
                'je.description',
            ])
            ->get()
            ->map(function ($row) {
                $balance = $row->debit - $row->credit;
                return [
                    'date' => \Carbon\Carbon::parse($row->entry_date),
                    'type' => $row->type,
                    'reference' => $row->reference,
                    'carrier' => $row->carrier_name ?? 'General',
                    'debit' => (float) $row->debit,
                    'credit' => (float) $row->credit,
                    'balance_impact' => $balance > 0 ? "(+\${$balance})" : "-\${$balance}",
                    'description' => $row->description,
                ];
            });
    }

    /**
     * Get list of carriers partner operates with (active partnerships)
     * 
     * @return Collection
     */
    public function getActiveCarriers(): Collection
    {
        return $this->partner->carriers()
            ->orderBy('name')
            ->get()
            ->map(function ($carrier) {
                $states = $this->partner->carrierStates()
                    ->where('insurance_carrier_id', $carrier->id)
                    ->pluck('state')
                    ->toArray();

                return [
                    'id' => $carrier->id,
                    'name' => $carrier->name,
                    'states' => $states,
                    'state_count' => count($states),
                ];
            });
    }

    /**
     * Get all authorized states partner can sell in
     * 
     * @return Collection
     */
    public function getAuthorizedStates(): Collection
    {
        return $this->partner->carrierStates()
            ->distinct()
            ->pluck('state')
            ->sort()
            ->values();
    }

    /**
     * Calculate estimated commission for a potential lead
     * 
     * @param float $monthlyPremium
     * @param string $state
     * @param int $carrierId
     * @param string $settlementType
     * @return float|null
     */
    public function estimateCommission(
        float $monthlyPremium,
        string $state,
        int $carrierId,
        string $settlementType = 'level'
    ): ?float {
        $carrierState = $this->partner->carrierStates()
            ->where('insurance_carrier_id', $carrierId)
            ->where('state', $state)
            ->first();

        if (!$carrierState) {
            return null;
        }

        $settlementPct = $carrierState->getSettlementPercentage($settlementType);
        
        if ($settlementPct === null) {
            return null;
        }

        return round($monthlyPremium * 9 * ($settlementPct / 100), 2);
    }
}
