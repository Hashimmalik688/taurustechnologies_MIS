<?php

namespace App\Services;

use App\Models\CarrierSheetEntry;
use App\Models\CarrierSheetOpeningCb;
use App\Models\CarrierSheetRate;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class CarrierSheetService
{
    /**
     * Cache TTL for carrier sheet summaries (15 minutes for better performance).
     */
    private const CACHE_TTL = 900;

    /**
     * Generate cache key for carrier summary.
     */
    private function getSummaryCacheKey(int $carrierId, ?string $periodMonth): string
    {
        return "carrier_sheet:summary:{$carrierId}:" . ($periodMonth ?? 'all');
    }

    /**
     * Generate cache key for dashboard summary.
     */
    private function getDashboardCacheKey(?string $periodMonth): string
    {
        return "carrier_sheet:dashboard:" . ($periodMonth ?? 'all');
    }

    /**
     * Clear all carrier sheet caches.
     */
    public function clearCache(?int $carrierId = null, ?string $periodMonth = null): void
    {
        if ($carrierId) {
            // Clear specific carrier cache
            Cache::forget($this->getSummaryCacheKey($carrierId, $periodMonth));
            Cache::forget($this->getSummaryCacheKey($carrierId, null));
            // Clear daily summary for this carrier
            Cache::forget("carrier_sheet:daily:{$carrierId}:" . ($periodMonth ?? 'all'));
        } else {
            // Clear all carrier sheet caches
            CarrierSheetRate::all()->each(function ($rate) {
                Cache::forget($this->getSummaryCacheKey($rate->id, null));
                Cache::forget("carrier_sheet:daily:{$rate->id}:all");
            });
        }
        
        // Always clear dashboard and metadata caches when any data changes
        Cache::forget($this->getDashboardCacheKey($periodMonth));
        Cache::forget($this->getDashboardCacheKey(null));
        Cache::forget('carrier_sheet:available_months');
        Cache::forget('carrier_sheet:active_carriers');
    }

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

    /**
     * Recalculate a single entry (updates commission + balance).
     * Clears cache for the related carrier.
     */
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

        // Clear cache for this carrier
        if ($entry->exists) {
            $this->clearCache($entry->carrier_sheet_rate_id, $entry->period_month?->format('Y-m-01'));
        }

        return $entry;
    }

    /**
     * Bulk recalculate all entries for a carrier (after rate change).
     * Uses batch updates for better performance.
     */
    public function recalculateAllEntries(CarrierSheetRate $rate, ?string $periodMonth = null): int
    {
        $query = $rate->entries();
        $this->scopeByPeriodMonth($query, $periodMonth);

        $entries = $query->get();
        $count = 0;
        $updates = [];

        foreach ($entries as $entry) {
            $entry->setRelation('carrierRate', $rate);
            $this->recalculateEntry($entry);
            
            // Collect updates for batch processing
            $updates[] = [
                'id' => $entry->id,
                'commission' => $entry->commission,
                'balance' => $entry->balance,
            ];
            
            $count++;
        }

        // Batch update all entries
        foreach ($updates as $update) {
            CarrierSheetEntry::where('id', $update['id'])->update([
                'commission' => $update['commission'],
                'balance' => $update['balance'],
            ]);
        }

        // Clear cache after bulk update
        $this->clearCache($rate->id, $periodMonth);

        return $count;
    }

    /**
     * CARRIER SUMMARY (Badge values for one carrier sheet)
     * Uses aggressive caching and optimized queries.
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
     */
    public function getCarrierSummary(CarrierSheetRate $rate, ?string $periodMonth = null, bool $useCache = true): array
    {
        // Check cache first
        if ($useCache) {
            $cached = Cache::get($this->getSummaryCacheKey($rate->id, $periodMonth));
            if ($cached !== null) {
                return $cached;
            }
        }

        // Optimized query - only select needed columns for calculations
        $query = $rate->entries()
            ->withoutTrashed()
            ->select(['id', 'status', 'commission', 'paid_amount', 'chargeback_amount']);
        $this->scopeByPeriodMonth($query, $periodMonth);
        $entries = $query->get();

        // No need to preload leads for summary calculations

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

        $result = [
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

        // Cache the result
        if ($useCache) {
            Cache::put($this->getSummaryCacheKey($rate->id, $periodMonth), $result, self::CACHE_TTL);
        }

        return $result;
    }

    /**
     * DASHBOARD SUMMARY (all carriers — replicates D.B sheet)
     * Uses aggressive caching and optimized queries.
     */
    public function getDashboardSummary(?string $periodMonth = null, bool $useCache = true): array
    {
        // Check cache first
        if ($useCache) {
            $cached = Cache::get($this->getDashboardCacheKey($periodMonth));
            if ($cached !== null) {
                return $cached;
            }
        }
        
        $carriers = Cache::remember(
            'carrier_sheet:active_carriers',
            3600,
            fn() => CarrierSheetRate::active()->ordered()->get()
        );
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

        $result = [
            'rows'   => $rows,
            'totals' => $totals,
        ];

        // Cache the result
        if ($useCache) {
            Cache::put($this->getDashboardCacheKey($periodMonth), $result, self::CACHE_TTL);
        }

        return $result;
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
        return $query->where(function ($q) use ($parsed) {
            // Entries with explicit period_month matching this month
            $q->where(function ($q2) use ($parsed) {
                $q2->whereNotNull('period_month')
                   ->whereYear('period_month', $parsed->year)
                   ->whereMonth('period_month', $parsed->month);
            })
            // OR entries with no period_month but entry_date in this month
            ->orWhere(function ($q2) use ($parsed) {
                $q2->whereNull('period_month')
                   ->whereYear('entry_date', $parsed->year)
                   ->whereMonth('entry_date', $parsed->month);
            });
        });
    }
}
