<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Services\CommissionCalculationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RevenueAnalyticsController extends Controller
{
    private function resolvePeriod(Request $request): array
    {
        if ($request->filled('month')) {
            $periodStart = Carbon::parse($request->month . '-01')->startOfDay();
            $periodEnd   = $periodStart->copy()->endOfMonth()->endOfDay();
        } elseif ($request->filled('start') && $request->filled('end')) {
            $periodStart = Carbon::parse($request->start)->startOfDay();
            $periodEnd   = Carbon::parse($request->end)->endOfDay();
        } else {
            $periodStart = Carbon::now()->startOfMonth()->startOfDay();
            $periodEnd   = Carbon::now()->endOfDay();
        }

        return [$periodStart, $periodEnd];
    }

    public function index(Request $request)
    {
        // ── Date Range ──────────────────────────────────────────────────────
        [$periodStart, $periodEnd] = $this->resolvePeriod($request);

        $periodLabel  = $periodStart->format('F Y');
        $prevMonth    = $periodStart->copy()->subMonth()->format('Y-m');
        $nextMonth    = $periodStart->copy()->addMonth()->format('Y-m');
        $currentMonth = Carbon::now()->format('Y-m');
        $activeMonth  = $periodStart->format('Y-m');

        $commissionService = app(CommissionCalculationService::class);

        // ── Projected Revenue (Pending Draft queue) ──────────────────────────
        // Pending Draft = followup done, not yet paid, not dead
        $pending_leads = Lead::whereNotNull('followup_done_at')
            ->whereNull('paid_at')
            ->whereNull('policy_died_at')
            ->whereBetween('sale_date', [$periodStart, $periodEnd])
            ->with(['partner'])
            ->get();

        $pending_count   = $pending_leads->count();
        $pending_premium = $pending_leads->sum(fn ($l) => $l->monthly_premium ?? 0);

        $projected_revenue       = 0;
        $lead_projected_revenues = [];

        foreach ($pending_leads as $lead) {
            $premium = (float) ($lead->monthly_premium ?? 0);

            // Resolve settlement type
            $raw  = strtolower(trim($lead->settlement_type ?: $lead->policy_type ?: ''));
            if (str_contains($raw, 'g.i') || str_contains($raw, 'gi')) $type = 'gi';
            elseif (str_contains($raw, 'grad'))   $type = 'graded';
            elseif (str_contains($raw, 'modif'))  $type = 'modified';
            else                                   $type = 'level';

            // premium × 9 × commission% from cluster rates
            $result = $commissionService->calculateCommission(
                $lead->partner_id,
                $lead->insurance_carrier_id,
                $lead->state ?? '',
                $type,
                $premium
            );
            $rev = $result['commission'] ?? 0;

            $projected_revenue                  += $rev;
            $lead_projected_revenues[$lead->id]  = round($rev, 2);
            $lead->calculated_revenue            = round($rev, 2);
        }

        // ── Monthly Trend (from pending drafts, grouped by sale_date) ────────
        $monthly_data = $pending_leads
            ->groupBy(function ($item) {
                if (!$item->sale_date) return 'Unknown';
                $date = is_string($item->sale_date)
                    ? $item->sale_date
                    : $item->sale_date->toDateString();
                return substr($date, 0, 7);
            })
            ->map(fn ($g) => [
                'count'   => $g->count(),
                'premium' => $g->sum(fn ($l) => $l->monthly_premium ?? 0),
                'revenue' => $g->sum(fn ($l) => $l->calculated_revenue ?? 0),
            ]);

        // ── Partner × Carrier Breakdown (from pending drafts) ────────────────
        $partner_carrier_breakdown = $pending_leads
            ->groupBy(fn ($l) => $l->partner_id ?? 0)
            ->map(function ($partnerGroup, $partnerId) {
                $partner     = $partnerGroup->first()->partner;
                $partnerName = $partner ? $partner->name : 'Unassigned';
                $partnerCode = $partner ? $partner->code : '—';

                $carriers = $partnerGroup
                    ->groupBy(fn ($l) => $l->carrier_name ?: 'Unknown Carrier')
                    ->map(fn ($cg, $name) => [
                        'carrier' => $name,
                        'count'   => $cg->count(),
                        'revenue' => $cg->sum(fn ($l) => $l->calculated_revenue ?? 0),
                        'premium' => $cg->sum(fn ($l) => $l->monthly_premium ?? 0),
                    ])
                    ->sortByDesc('revenue')
                    ->values();

                return [
                    'partner_id'    => $partnerId,
                    'partner_name'  => $partnerName,
                    'partner_code'  => $partnerCode,
                    'carriers'      => $carriers,
                    'total_revenue' => $carriers->sum('revenue'),
                    'total_count'   => $carriers->sum('count'),
                ];
            })
            ->sortByDesc('total_revenue')
            ->values();

        // ── Top Closers by Projected Revenue ────────────────────────────────
        $top_closers = $pending_leads
            ->groupBy(fn ($l) => $l->closer_name ?: ($l->managed_by ? 'Agent #' . $l->managed_by : 'Unknown'))
            ->map(fn ($g, $name) => [
                'name'    => $name,
                'count'   => $g->count(),
                'revenue' => $g->sum(fn ($l) => $l->calculated_revenue ?? 0),
            ])
            ->sortByDesc('revenue')
            ->take(10)
            ->values();

        return view('admin.revenue-analytics.index', compact(
            'periodLabel', 'prevMonth', 'nextMonth', 'currentMonth', 'activeMonth',
            'periodStart', 'periodEnd',
            'pending_count', 'projected_revenue', 'pending_premium',
            'monthly_data',
            'pending_leads',
            'lead_projected_revenues',
            'partner_carrier_breakdown',
            'top_closers',
        ));
    }

    public function liveData(Request $request)
    {
        [$periodStart, $periodEnd] = $this->resolvePeriod($request);

        $commissionService = app(CommissionCalculationService::class);

        $pendingLeads = Lead::whereNotNull('followup_done_at')
            ->whereNull('paid_at')
            ->whereNull('policy_died_at')
            ->whereBetween('sale_date', [$periodStart, $periodEnd])
            ->with(['partner'])
            ->get();

        $pendingCount = $pendingLeads->count();
        $pendingPremium = (float) $pendingLeads->sum(fn ($l) => $l->monthly_premium ?? 0);

        $projectedRevenue = 0.0;
        foreach ($pendingLeads as $lead) {
            $premium = (float) ($lead->monthly_premium ?? 0);
            $raw = strtolower(trim($lead->settlement_type ?: $lead->policy_type ?: ''));
            if (str_contains($raw, 'g.i') || str_contains($raw, 'gi')) $type = 'gi';
            elseif (str_contains($raw, 'grad')) $type = 'graded';
            elseif (str_contains($raw, 'modif')) $type = 'modified';
            else $type = 'level';

            $result = $commissionService->calculateCommission(
                $lead->partner_id,
                $lead->insurance_carrier_id,
                $lead->state ?? '',
                $type,
                $premium
            );
            $projectedRevenue += (float) ($result['commission'] ?? 0);
        }

        return response()->json([
            'pending_count' => $pendingCount,
            'pending_premium' => round($pendingPremium, 2),
            'projected_revenue' => round($projectedRevenue, 2),
            'period_label' => $periodStart->format('F Y'),
            'updated_at' => now('America/Los_Angeles')->format('M d, Y h:i:s A') . ' PT',
        ]);
    }
}
