<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Partner;
use App\Services\CommissionCalculationService;
use Illuminate\Http\Request;

/**
 * Per-Partner Commission Report
 *
 * Simple breakdown of estimated commission by partner for a given
 * 3rd→3rd period — same period logic and commission math as the
 * "Est. Commission" KPI on the Executive Dashboard.
 */
class PartnerCommissionReportController extends Controller
{
    private function resolveSettlementKey($lead): string
    {
        $raw = strtolower(trim($lead->settlement_type ?: $lead->policy_type ?: ''));
        if (str_contains($raw, 'g.i') || str_contains($raw, 'gi')) return 'gi';
        if (str_contains($raw, 'grad')) return 'graded';
        if (str_contains($raw, 'modif')) return 'modified';
        return 'level';
    }

    private function calcLeadCommission($lead, CommissionCalculationService $service): float
    {
        $premium = (float) ($lead->monthly_premium ?? 0);
        if ($premium <= 0 || empty($lead->partner_id) || empty($lead->insurance_carrier_id)) {
            return 0.0;
        }
        $result = $service->calculateCommission(
            (int) $lead->partner_id,
            (int) $lead->insurance_carrier_id,
            $lead->state ?? '',
            $this->resolveSettlementKey($lead),
            $premium
        );
        return round($result['commission'] ?? 0, 2);
    }

    public function index(Request $request)
    {
        $periodParam = $request->get('period');
        if ($periodParam && preg_match('/^\d{4}-\d{2}$/', $periodParam)) {
            $anchor = \Carbon\Carbon::createFromFormat('Y-m', $periodParam)->setDay(3);
        } else {
            $anchor = today();
        }
        if ($anchor->day >= 3) {
            $periodStart = $anchor->copy()->setDay(3)->startOfDay();
            $periodEnd   = $anchor->copy()->addMonthNoOverflow()->setDay(3)->endOfDay();
        } else {
            $periodStart = $anchor->copy()->subMonthNoOverflow()->setDay(3)->startOfDay();
            $periodEnd   = $anchor->copy()->setDay(3)->endOfDay();
        }

        $periodLabel    = $periodStart->format('M j') . ' → ' . $periodEnd->format('M j, Y');
        $selectedPeriod = $periodStart->format('Y-m');
        $prevPeriod     = $periodStart->copy()->subMonthNoOverflow()->format('Y-m');
        $nextPeriod     = $periodStart->copy()->addMonthNoOverflow()->format('Y-m');
        $isCurrentPeriod = today()->betweenIncluded($periodStart, $periodEnd);

        $leads = Lead::whereNotNull('pending_contract_at')
            ->whereDate('pending_contract_at', '>=', $periodStart)
            ->whereDate('pending_contract_at', '<=', $periodEnd)
            ->get(['id', 'monthly_premium', 'assigned_partner', 'partner_id',
                   'insurance_carrier_id', 'state', 'settlement_type', 'policy_type']);

        $service    = app(CommissionCalculationService::class);
        $partnerIds = $leads->pluck('partner_id')->filter()->unique();
        $partners   = Partner::whereIn('id', $partnerIds)->get()->keyBy('id');

        $groups = $leads->groupBy(fn ($l) => $l->partner_id ?: ('name:' . ($l->assigned_partner ?: 'Unassigned')));

        $rows = $groups->map(function ($group) use ($partners, $service) {
            $first   = $group->first();
            $partner = $first->partner_id ? $partners->get($first->partner_id) : null;

            $commission = 0.0;
            foreach ($group as $lead) {
                $commission += $this->calcLeadCommission($lead, $service);
            }

            return [
                'partner_id'   => $partner->id ?? null,
                'partner_name' => $partner->name ?? ($first->assigned_partner ?: 'Unassigned'),
                'sales_count'  => $group->count(),
                'premium'      => (float) $group->sum('monthly_premium'),
                'commission'   => round($commission, 2),
            ];
        })
            ->sortByDesc('commission')
            ->values();

        $totals = [
            'sales_count' => $rows->sum('sales_count'),
            'premium'     => $rows->sum('premium'),
            'commission'  => $rows->sum('commission'),
        ];

        $canViewLedger = $request->user()->canViewModule('accounting');

        return view('admin.reports.partner-commission-report', compact(
            'rows',
            'totals',
            'periodLabel',
            'selectedPeriod',
            'prevPeriod',
            'nextPeriod',
            'isCurrentPeriod',
            'canViewLedger'
        ));
    }
}
