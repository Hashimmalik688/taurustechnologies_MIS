<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Services\CommissionCalculationService;
use App\Support\Statuses;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RevenueAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        // ── Date Range ──────────────────────────────────────────────────────
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

        $periodLabel  = $periodStart->format('F Y');
        $prevMonth    = $periodStart->copy()->subMonth()->format('Y-m');
        $nextMonth    = $periodStart->copy()->addMonth()->format('Y-m');
        $currentMonth = Carbon::now()->format('Y-m');
        $activeMonth  = $periodStart->format('Y-m');

        // ── Confirmed Revenue (Issued) ───────────────────────────────────────
        $issued_sales = Lead::where('status', Statuses::LEAD_ACCEPTED)
            ->where('submission_status', Statuses::SUB_APPROVED)
            ->where('issuance_status', Statuses::ISSUANCE_ISSUED)
            ->whereBetween('issuance_date', [$periodStart, $periodEnd])
            ->with(['partner', 'insuranceCarrier'])
            ->get();

        $total_count   = $issued_sales->count();
        $total_premium = $issued_sales->sum(fn ($l) => $l->monthly_premium ?? 0);
        $total_revenue = $issued_sales->sum(fn ($l) => $l->agent_revenue ?? $l->monthly_premium ?? 0);
        $avg_revenue   = $total_count > 0 ? $total_revenue / $total_count : 0;

        // ── Projected Revenue (Pending Drafts — same workflow as PendingDraftController) ─
        // Pending Draft = followup done, draft not yet hit (paid_at IS NULL, policy_died_at IS NULL)
        $pending_leads = Lead::whereNotNull('followup_done_at')
            ->whereNull('paid_at')
            ->whereNull('policy_died_at')
            ->whereBetween('sale_date', [$periodStart, $periodEnd])
            ->with(['partner', 'insuranceCarrier'])
            ->get();

        $pending_count = $pending_leads->count();

        /** @var CommissionCalculationService $commissionService */
        $commissionService = app(CommissionCalculationService::class);
        $projected_revenue = 0;

        foreach ($pending_leads as $lead) {
            if ($lead->agent_revenue) {
                // Already calculated — use it
                $projected_revenue += $lead->agent_revenue;
            } elseif ($lead->partner_id && $lead->insurance_carrier_id && $lead->monthly_premium) {
                // Use cluster formula: premium × 9 × cluster commission %
                $settlementType = in_array($lead->settlement_type, ['level', 'graded', 'gi', 'modified'])
                    ? $lead->settlement_type : 'level';
                $result = $commissionService->calculateCommission(
                    $lead->partner_id,
                    $lead->insurance_carrier_id,
                    $lead->state ?? '',
                    $settlementType,
                    (float) $lead->monthly_premium
                );
                $projected_revenue += $result['commission'] ?? ($lead->monthly_premium ?? 0);
            } else {
                $projected_revenue += $lead->monthly_premium ?? 0;
            }
        }

        // ── Monthly Trend ────────────────────────────────────────────────────
        $monthly_data = $issued_sales
            ->groupBy(function ($item) {
                if (!$item->issuance_date) return 'Unknown';
                $date = is_string($item->issuance_date)
                    ? $item->issuance_date
                    : $item->issuance_date->toDateString();
                return substr($date, 0, 7);
            })
            ->map(fn ($g) => [
                'count'   => $g->count(),
                'premium' => $g->sum(fn ($l) => $l->monthly_premium ?? 0),
                'revenue' => $g->sum(fn ($l) => $l->agent_revenue ?? $l->monthly_premium ?? 0),
            ]);

        // ── Partner × Carrier Breakdown ──────────────────────────────────────
        $partner_carrier_breakdown = $issued_sales
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
                        'revenue' => $cg->sum(fn ($l) => $l->agent_revenue ?? $l->monthly_premium ?? 0),
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

        // ── Top Closers by Revenue ───────────────────────────────────────────
        $top_closers = $issued_sales
            ->groupBy(fn ($l) => $l->closer_name ?: ($l->managed_by ? 'Agent #' . $l->managed_by : 'Unknown'))
            ->map(fn ($g, $name) => [
                'name'    => $name,
                'count'   => $g->count(),
                'revenue' => $g->sum(fn ($l) => $l->agent_revenue ?? $l->monthly_premium ?? 0),
            ])
            ->sortByDesc('revenue')
            ->take(10)
            ->values();

        return view('admin.revenue-analytics.index', compact(
            'periodLabel', 'prevMonth', 'nextMonth', 'currentMonth', 'activeMonth',
            'periodStart', 'periodEnd',
            'total_count', 'total_premium', 'total_revenue', 'avg_revenue',
            'pending_count', 'projected_revenue',
            'monthly_data',
            'issued_sales',
            'pending_leads',
            'partner_carrier_breakdown',
            'top_closers',
        ));
    }
}
