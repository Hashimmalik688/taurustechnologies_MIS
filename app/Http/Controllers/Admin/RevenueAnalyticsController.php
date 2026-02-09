<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;

class RevenueAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        // Get all issued and verified sales
        $issued_sales = Lead::where('status', 'accepted')
            ->where('manager_status', 'approved')
            ->where('issuance_status', 'Issued')
            ->get();

        // Calculate revenue by verification status using agent_revenue (calculated commission)
        // Fallback to monthly_premium if agent_revenue is not calculated yet
        $good_revenue = $issued_sales
            ->where('bank_verification_status', 'Good')
            ->sum(function($lead) {
                return $lead->agent_revenue ?? $lead->monthly_premium ?? 0;
            });

        $average_revenue = $issued_sales
            ->where('bank_verification_status', 'Average')
            ->sum(function($lead) {
                return $lead->agent_revenue ?? $lead->monthly_premium ?? 0;
            });

        $bad_revenue = $issued_sales
            ->where('bank_verification_status', 'Bad')
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
        $good_count = $issued_sales->where('bank_verification_status', 'Good')->count();
        $average_count = $issued_sales->where('bank_verification_status', 'Average')->count();
        $bad_count = $issued_sales->where('bank_verification_status', 'Bad')->count();
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
                    'good' => $group->where('bank_verification_status', 'Good')->sum(function($lead) {
                        return $lead->agent_revenue ?? $lead->monthly_premium ?? 0;
                    }),
                    'average' => $group->where('bank_verification_status', 'Average')->sum(function($lead) {
                        return $lead->agent_revenue ?? $lead->monthly_premium ?? 0;
                    }),
                    'bad' => $group->where('bank_verification_status', 'Bad')->sum(function($lead) {
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
        $verifiers = User::role('Verifier')->get();
        $verifier_stats = [];
        
        foreach ($verifiers as $verifier) {
            // Get leads assigned to this verifier
            $verifier_leads = Lead::where('verified_by', $verifier->id)
                ->where('bank_verification_status', '!=', null)
                ->get();
            
            $total_submitted = $verifier_leads->count();
            $pending_callbacks = $verifier_leads->where('bank_verification_status', null)->count();
            $declined_calls = $verifier_leads->where('decline_reason', '!=', null)->count();
            $marked_as_sale = $verifier_leads->where('status', 'accepted')->count();
            
            // Calculate transfer rate
            $transferred = $verifier_leads->where('status', 'accepted')->count();
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

        return view('admin.revenue-analytics.index', compact(
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
        ));
    }
}
