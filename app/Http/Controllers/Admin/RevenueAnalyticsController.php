<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\User;
use App\Support\Roles;
use App\Support\Statuses;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RevenueAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        // ── Date Range (1st-to-1st month window, default = current month) ──
        if ($request->filled('month')) {
            // ?month=YYYY-MM  → full calendar month
            $periodStart = Carbon::parse($request->month . '-01')->startOfDay();
            $periodEnd   = $periodStart->copy()->endOfMonth()->endOfDay();
        } elseif ($request->filled('start') && $request->filled('end')) {
            // custom range
            $periodStart = Carbon::parse($request->start)->startOfDay();
            $periodEnd   = Carbon::parse($request->end)->endOfDay();
        } else {
            // default: current month 1st → today
            $periodStart = Carbon::now()->startOfMonth()->startOfDay();
            $periodEnd   = Carbon::now()->endOfDay();
        }

        $periodLabel    = $periodStart->format('F Y');
        $prevMonth      = $periodStart->copy()->subMonth()->format('Y-m');
        $nextMonth      = $periodStart->copy()->addMonth()->format('Y-m');
        $currentMonth   = Carbon::now()->format('Y-m');
        $activeMonth    = $periodStart->format('Y-m');

        // Get all issued and verified sales (eager-load partner for breakdown table)
        $issued_sales = Lead::where('status', Statuses::LEAD_ACCEPTED)
            ->where('manager_status', Statuses::MGR_APPROVED)
            ->where('issuance_status', Statuses::ISSUANCE_ISSUED)
            ->whereBetween('issuance_date', [$periodStart, $periodEnd])
            ->with('partner')
            ->get();

        // Calculate revenue by verification status using agent_revenue (calculated commission)
        // Fallback to monthly_premium if agent_revenue is not calculated yet
        $good_revenue = $issued_sales
            ->where('bank_verification_status', Statuses::BANK_GOOD)
            ->sum(function($lead) {
                return $lead->agent_revenue ?? $lead->monthly_premium ?? 0;
            });

        $average_revenue = $issued_sales
            ->where('bank_verification_status', Statuses::BANK_AVERAGE)
            ->sum(function($lead) {
                return $lead->agent_revenue ?? $lead->monthly_premium ?? 0;
            });

        $bad_revenue = $issued_sales
            ->where('bank_verification_status', Statuses::BANK_BAD)
            ->sum(function($lead) {
                return $lead->agent_revenue ?? $lead->monthly_premium ?? 0;
            });

        $unverified_revenue = $issued_sales
            ->whereNull('bank_verification_status')
            ->sum(function($lead) {
                return $lead->agent_revenue ?? $lead->monthly_premium ?? 0;
            });

        $total_revenue = $good_revenue + $average_revenue + $bad_revenue + $unverified_revenue;

        // Calculate counts
        $good_count = $issued_sales->where('bank_verification_status', Statuses::BANK_GOOD)->count();
        $average_count = $issued_sales->where('bank_verification_status', Statuses::BANK_AVERAGE)->count();
        $bad_count = $issued_sales->where('bank_verification_status', Statuses::BANK_BAD)->count();
        $unverified_count = $issued_sales->whereNull('bank_verification_status')->count();
        $total_count = $good_count + $average_count + $bad_count + $unverified_count;

        // Calculate percentages
        $good_percentage = $total_count > 0 ? ($good_count / $total_count) * 100 : 0;
        $average_percentage = $total_count > 0 ? ($average_count / $total_count) * 100 : 0;
        $bad_percentage = $total_count > 0 ? ($bad_count / $total_count) * 100 : 0;
        $unverified_percentage = $total_count > 0 ? ($unverified_count / $total_count) * 100 : 0;

        // Revenue percentages
        $good_revenue_percentage = $total_revenue > 0 ? ($good_revenue / $total_revenue) * 100 : 0;
        $average_revenue_percentage = $total_revenue > 0 ? ($average_revenue / $total_revenue) * 100 : 0;
        $bad_revenue_percentage = $total_revenue > 0 ? ($bad_revenue / $total_revenue) * 100 : 0;
        $unverified_revenue_percentage = $total_revenue > 0 ? ($unverified_revenue / $total_revenue) * 100 : 0;

        // Get monthly breakdown
        $monthly_data = $issued_sales
            ->groupBy(function($item) {
                if (!$item->issuance_date) {
                    return 'Unknown';
                }
                $date = is_string($item->issuance_date) ? strtotime($item->issuance_date) : $item->issuance_date;
                return is_string($item->issuance_date) ? date('Y-m', $date) : $date->format('Y-m');
            })
            ->map(function($group) {
                return [
                    'good' => $group->where('bank_verification_status', Statuses::BANK_GOOD)->sum(function($lead) {
                        return $lead->agent_revenue ?? $lead->monthly_premium ?? 0;
                    }),
                    'average' => $group->where('bank_verification_status', Statuses::BANK_AVERAGE)->sum(function($lead) {
                        return $lead->agent_revenue ?? $lead->monthly_premium ?? 0;
                    }),
                    'bad' => $group->where('bank_verification_status', Statuses::BANK_BAD)->sum(function($lead) {
                        return $lead->agent_revenue ?? $lead->monthly_premium ?? 0;
                    }),
                    'unverified' => $group->whereNull('bank_verification_status')->sum(function($lead) {
                        return $lead->agent_revenue ?? $lead->monthly_premium ?? 0;
                    }),
                    'total' => $group->sum(function($lead) {
                        return $lead->agent_revenue ?? $lead->monthly_premium ?? 0;
                    }),
                ];
            });

        // Get Verifier Stats
        $verifiers = User::role(Roles::VERIFIER)->get();
        $verifier_stats = [];
        
        foreach ($verifiers as $verifier) {
            // Get leads assigned to this verifier
            $verifier_leads = Lead::where('verified_by', $verifier->id)
                ->where('bank_verification_status', '!=', null)
                ->get();
            
            $total_submitted = $verifier_leads->count();
            $pending_callbacks = $verifier_leads->where('bank_verification_status', null)->count();
            $declined_calls = $verifier_leads->where('decline_reason', '!=', null)->count();
            $marked_as_sale = $verifier_leads->where('status', Statuses::LEAD_ACCEPTED)->count();
            
            // Calculate transfer rate
            $transferred = $verifier_leads->where('status', Statuses::LEAD_ACCEPTED)->count();
            $transfer_rate = $total_submitted > 0 ? ($transferred / $total_submitted) * 100 : 0;
            
            $verifier_stats[] = [
                'id' => $verifier->id,
                'name' => $verifier->name,
                'email' => $verifier->email,
                'total_submitted' => $total_submitted,
                'transferred' => $transferred,
                'pending_callbacks' => $pending_callbacks,
                'declined_calls' => $declined_calls,
                'marked_as_sale' => $marked_as_sale,
                'transfer_rate' => $transfer_rate,
            ];
        }

        // ── Partner × Carrier Revenue Breakdown ──────────────────────────────
        // Group issued sales by partner → then by carrier to show revenue per cluster
        $partner_carrier_breakdown = $issued_sales
            ->groupBy(function ($lead) {
                return $lead->partner_id ?? 0; // 0 = no partner assigned
            })
            ->map(function ($partnerGroup, $partnerId) {
                $partner = $partnerGroup->first()->partner;
                $partnerName = $partner ? $partner->name : 'Unassigned';
                $partnerCode = $partner ? $partner->code : '—';

                $carriers = $partnerGroup
                    ->groupBy(function ($lead) {
                        return $lead->carrier_name ?: 'Unknown Carrier';
                    })
                    ->map(function ($carrierGroup, $carrierName) {
                        $revenue = $carrierGroup->sum(function ($lead) {
                            return $lead->agent_revenue ?? $lead->monthly_premium ?? 0;
                        });
                        $premium = $carrierGroup->sum(fn ($l) => $l->monthly_premium ?? 0);
                        return [
                            'carrier'  => $carrierName,
                            'count'    => $carrierGroup->count(),
                            'revenue'  => $revenue,
                            'premium'  => $premium,
                        ];
                    })
                    ->sortByDesc('revenue')
                    ->values();

                $totalRevenue = $carriers->sum('revenue');
                $totalCount   = $carriers->sum('count');

                return [
                    'partner_id'   => $partnerId,
                    'partner_name' => $partnerName,
                    'partner_code' => $partnerCode,
                    'carriers'     => $carriers,
                    'total_revenue'=> $totalRevenue,
                    'total_count'  => $totalCount,
                ];
            })
            ->sortByDesc('total_revenue')
            ->values();

        return view('admin.revenue-analytics.index', compact(
            'periodLabel',
            'prevMonth',
            'nextMonth',
            'currentMonth',
            'activeMonth',
            'periodStart',
            'periodEnd',
            'good_revenue',
            'average_revenue',
            'bad_revenue',
            'unverified_revenue',
            'total_revenue',
            'good_count',
            'average_count',
            'bad_count',
            'unverified_count',
            'total_count',
            'good_percentage',
            'average_percentage',
            'bad_percentage',
            'unverified_percentage',
            'good_revenue_percentage',
            'average_revenue_percentage',
            'bad_revenue_percentage',
            'unverified_revenue_percentage',
            'monthly_data',
            'verifier_stats',
            'issued_sales',
            'partner_carrier_breakdown',
        ));
    }
}
