<?php

namespace App\Services;

use App\Models\CarrierSheetEntry;
use App\Models\CarrierSheetOpeningCb;
use App\Models\CarrierSheetRate;
use Illuminate\Support\Collection;

class CarrierSheetService
{
    /* ================================================================
     *  COMMISSION FORMULA
     *  Replicates: Premium × Multiplier × Rate / 2
     *
     *  DECLINED            → null  (commission hidden)
     *  CHARGEBACK + unpaid → null  (commission hidden)
     *  No policy type      → null
     *  Rate missing        → 'TBD'
     *  SEC GI              → multiplier 1 (all others 9)
     *  AIG E-1 overrides   → use rate_override instead of carrier rate
     * ================================================================ */
    public function calculateCommission(
        CarrierSheetRate $rate,
        ?string $policyType,
        float $premium,
        string $status,
        float $paidAmount = 0,
        ?float $rateOverride = null
    ): ?float {
        $status = strtolower(trim($status));

        // DECLINED → commission is null
        if ($status === 'declined') {
            return null;
        }

        // CHARGEBACK with no paid amount → commission is null
        if ($status === 'chargeback' && $paidAmount <= 0) {
            return null;
        }

        // No policy type → null
        if (!$policyType || trim($policyType) === '') {
            return null;
        }

        // Determine the rate to use
        $commissionRate = $rateOverride ?? $rate->getRateForType($policyType);

        if ($commissionRate === null) {
            return null; // TBD — rate not configured
        }

        // Multiplier: GI uses carrier-specific multiplier, all others use 9
        $multiplier = $rate->getMultiplier($policyType);

        return round($premium * $multiplier * $commissionRate / 2, 2);
    }

    /* ================================================================
     *  BALANCE FORMULA
     *  balance = (commission ?? 0) - effectivePaid - chargeback
     *
     *  paid_amount is only counted when status = paid or chargeback.
     *  For approved/declined the paid_amount field may be stored but
     *  should not affect balance or the PAID badge total.
     *
     *  APPROVED  → commission  (paid ignored)
     *  PAID      → commission - paid
     *  CHARGEBACK was-paid → commission - paid - cb
     *  CHARGEBACK unpaid   → 0 - 0 - cb = -cb
     *  DECLINED  → 0
     * ================================================================ */
    public function calculateBalance(
        string $status,
        ?float $commission,
        float $paidAmount,
        float $chargebackAmount
    ): float {
        $s = strtolower(trim($status));
        $effectivePaid = ($s === 'paid' || $s === 'chargeback') ? $paidAmount : 0.0;
        return round(($commission ?? 0) - $effectivePaid - $chargebackAmount, 2);
    }

    /* ================================================================
     *  RECALCULATE a single entry (updates commission + balance)
     * ================================================================ */
    public function recalculateEntry(CarrierSheetEntry $entry): CarrierSheetEntry
    {
        $rate = $entry->carrierRate ?? CarrierSheetRate::find($entry->carrier_sheet_rate_id);

        $entry->commission = $this->calculateCommission(
            $rate,
            $entry->policy_type,
            (float) $entry->premium,
            $entry->status,
            (float) $entry->paid_amount,
            $entry->rate_override ? (float) $entry->rate_override : null
        );

        $entry->balance = $this->calculateBalance(
            $entry->status,
            $entry->commission,
            (float) $entry->paid_amount,
            (float) $entry->chargeback_amount
        );

        return $entry;
    }

    /* ================================================================
     *  BULK RECALCULATE all entries for a carrier (after rate change)
     * ================================================================ */
    public function recalculateAllEntries(CarrierSheetRate $rate, ?string $periodMonth = null): int
    {
        $query = $rate->entries();
        $this->scopeByPeriodMonth($query, $periodMonth);

        $entries = $query->get();
        $count = 0;

        foreach ($entries as $entry) {
            $entry->setRelation('carrierRate', $rate);
            $this->recalculateEntry($entry);
            $entry->save();
            $count++;
        }

        return $count;
    }

    /* ================================================================
     *  CARRIER SUMMARY (Badge values for one carrier sheet)
     *
     *  K1 = SUMIF(status<>CHARGEBACK, commission) + SUMIFS(commission, status=CHARGEBACK, paid>0)
     *  L1 = SUM(paid_amount)
     *  M1 = K1 - L1 - N1
     *  N1 = SUM(chargeback_amount) + opening_cb
     *  P1 = count(entries)
     *  Q1 = count(status=PAID)
     *  R1 = count(status=APPROVED)
     *  S1 = count(status=CHARGEBACK)
     *  T1 = count(status=DECLINED)
     * ================================================================ */
    public function getCarrierSummary(CarrierSheetRate $rate, ?string $periodMonth = null): array
    {
        $query = $rate->entries()->withoutTrashed();
        $this->scopeByPeriodMonth($query, $periodMonth);
        $entries = $query->get();

        // K1: commission for non-chargeback statuses + commission for paid chargebacks
        $commissionTotal = 0;
        foreach ($entries as $e) {
            if (strtolower($e->status) !== 'chargeback') {
                $commissionTotal += (float) ($e->commission ?? 0);
            } elseif ((float) $e->paid_amount > 0) {
                $commissionTotal += (float) ($e->commission ?? 0);
            }
        }

        // Only count paid_amount for entries that are actually paid or chargeback-of-paid
        $paidTotal = $entries
            ->filter(fn ($e) => in_array(strtolower($e->status), ['paid', 'chargeback']))
            ->sum(fn ($e) => (float) $e->paid_amount);
        $chargebackTotal = $entries->sum(fn ($e) => (float) $e->chargeback_amount);

        // Add opening chargeback and opening balance
        $openingCb = 0;
        $openingBalance = 0;
        if ($periodMonth) {
            $ocb = CarrierSheetOpeningCb::where('carrier_sheet_rate_id', $rate->id)
                ->where('period_month', $periodMonth)
                ->first();
            $openingCb      = $ocb ? (float) $ocb->amount : 0;
            $openingBalance = $ocb ? (float) $ocb->opening_balance : 0;
        } else {
            $openingCb      = CarrierSheetOpeningCb::where('carrier_sheet_rate_id', $rate->id)->sum('amount');
            $openingBalance = CarrierSheetOpeningCb::where('carrier_sheet_rate_id', $rate->id)->sum('opening_balance');
        }

        $chargebackTotal += $openingCb;
        $balanceTotal = round($commissionTotal - $paidTotal - $chargebackTotal + $openingBalance, 2);

        return [
            'commission'       => round($commissionTotal, 2),    // K1
            'paid'             => round($paidTotal, 2),           // L1
            'balance'          => $balanceTotal,                   // M1
            'chargeback_total' => round($chargebackTotal, 2),     // N1
            'opening_cb'       => round($openingCb, 2),
            'opening_balance'  => round($openingBalance, 2),
            'total_apps'       => $entries->count(),               // P1
            'paid_count'       => $entries->where('status', 'paid')->count(),          // Q1
            'approved_count'   => $entries->where('status', 'approved')->count(),      // R1
            'chargeback_count' => $entries->where('status', 'chargeback')->count(),    // S1
            'declined_count'   => $entries->where('status', 'declined')->count(),      // T1
        ];
    }

    /* ================================================================
     *  DASHBOARD SUMMARY (all carriers — replicates D.B sheet)
     * ================================================================ */
    public function getDashboardSummary(?string $periodMonth = null): array
    {
        $carriers = CarrierSheetRate::active()->ordered()->get();
        $rows = [];
        $totals = [
            'commission' => 0, 'paid' => 0, 'balance' => 0,
            'chargeback_total' => 0, 'total_apps' => 0, 'paid_count' => 0,
            'approved_count' => 0, 'chargeback_count' => 0, 'declined_count' => 0,
        ];

        foreach ($carriers as $carrier) {
            $summary = $this->getCarrierSummary($carrier, $periodMonth);
            $rows[] = array_merge(['carrier' => $carrier], $summary);

            foreach ($totals as $key => &$val) {
                $val += $summary[$key] ?? 0;
            }
        }

        // Recalculate balance total from commission - paid - chargeback
        $totals['balance'] = round($totals['commission'] - $totals['paid'] - $totals['chargeback_total'], 2);

        return [
            'rows'   => $rows,
            'totals' => $totals,
        ];
    }

    /* ================================================================
     *  DAILY SUMMARY (entries grouped by date for one carrier)
     * ================================================================ */
    public function getDailySummary(CarrierSheetRate $rate, ?string $periodMonth = null): Collection
    {
        $query = $rate->entries()->withoutTrashed();
        $this->scopeByPeriodMonth($query, $periodMonth);

        return $query->get()
            ->groupBy(fn ($e) => $e->entry_date?->format('Y-m-d'))
            ->map(fn ($group, $date) => [
                'date'       => $date,
                'apps'       => $group->count(),
                'commission' => round($group->sum(fn ($e) => (float) ($e->commission ?? 0)), 2),
            ])
            ->sortKeys()
            ->values();
    }

    /* ================================================================
     *  AVAILABLE MONTHS — distinct period_months across all entries
     * ================================================================ */
    public function getAvailableMonths(?int $carrierId = null): Collection
    {
        $query = CarrierSheetEntry::withoutTrashed()
            ->whereNotNull('period_month')
            ->select('period_month')
            ->distinct()
            ->orderBy('period_month', 'desc');

        if ($carrierId) {
            $query->where('carrier_sheet_rate_id', $carrierId);
        }

        return $query->pluck('period_month')->map(fn ($d) => \Carbon\Carbon::parse($d)->format('Y-m-01'));
    }

    /* ================================================================
     *  SCOPE BY PERIOD MONTH — uses year+month to avoid datetime
     *  precision mismatches (period_month stored as full timestamp)
     * ================================================================ */
    private function scopeByPeriodMonth($query, ?string $periodMonth)
    {
        if (!$periodMonth) {
            return $query;
        }
        $parsed = \Carbon\Carbon::parse($periodMonth);
        return $query->whereYear('period_month', $parsed->year)
                     ->whereMonth('period_month', $parsed->month);
    }
}
