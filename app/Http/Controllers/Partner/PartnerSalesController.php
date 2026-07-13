<?php

namespace App\Http\Controllers\Partner;

use App\Events\LeadCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePartnerSaleRequest;
use App\Models\Lead;
use App\Support\Statuses;
use App\Support\Teams;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Partner-side sale intake + tracking.
 *
 * A partner company's closers fill the same sale form our internal closers use;
 * the submission lands in our sales pipeline as a normal Lead, attributed to the
 * submitting partner. The company sees a roll-up across all its closers.
 */
class PartnerSalesController extends Controller
{
    /**
     * Show the sale-submission form.
     */
    public function create()
    {
        $partner = Auth::guard('partner')->user();
        $this->authorizeSubmitter($partner);

        return view('partner.sales-entry.create', compact('partner'));
    }

    /**
     * Persist a partner-submitted sale as a Lead in our pipeline.
     */
    public function store(StorePartnerSaleRequest $request)
    {
        $partner = Auth::guard('partner')->user();
        $this->authorizeSubmitter($partner);

        $company = $partner->company();

        $leadData = $request->validated();

        // Carrier goes on the related carrier row, not the lead itself.
        $carrierData = [
            'name'            => $leadData['carrier_name'] ?? null,
            'coverage_amount' => $leadData['coverage_amount'] ?? null,
            'premium_amount'  => $leadData['monthly_premium'] ?? null,
            'status'          => 'pending',
        ];
        unset($leadData['carrier_name']);

        // Partner submissions enter the Peregrine pipeline, same as internal sales.
        $leadData['source']      = 'Partner Portal';
        $leadData['source_type'] = Teams::PEREGRINE;
        $leadData['team']        = Teams::PEREGRINE;

        // Attribution: partner_id drives the existing per-partner dashboards.
        // A closer's own id is used so the company roll-up (itself + closers) works.
        $leadData['partner_id']       = $partner->id;
        $leadData['assigned_partner'] = $company->name;
        $leadData['closer_name']      = $leadData['closer_name'] ?? $partner->name;
        $leadData['closer_id']        = null; // partner closers are not employee users
        $leadData['verified_by']      = null;

        // 'closed' = form submitted by closer (NOT a completed sale), matching internal intake.
        $leadData['status'] = Statuses::LEAD_CLOSED;

        if (isset($leadData['smoker'])) {
            $leadData['smoker'] = $leadData['smoker'] ? 'yes' : 'no';
        }

        $lead = Lead::create($leadData);

        if (! empty($carrierData['name'])) {
            $lead->carriers()->create($carrierData);
        }

        event(new LeadCreated($lead, $partner->name));

        return redirect()->route('partner.submissions')
            ->with('success', 'Sale submitted successfully. You can track its status below.');
    }

    /**
     * Tracking table: what this partner (or company) has sent and its status.
     */
    public function submissions(Request $request)
    {
        $partner = Auth::guard('partner')->user();

        $scopeIds = $partner->salesScopeIds();
        $search   = $request->get('search');

        $base = Lead::whereIn('partner_id', $scopeIds)
            ->when($search, fn ($q) => $q->where('cn_name', 'like', '%' . $search . '%'));

        // Status buckets — simple, closer-facing labels over the raw pipeline fields.
        $summary = [
            'total'      => (clone $base)->count(),
            'submitted'  => (clone $base)->whereNull('pending_contract_at')
                                ->where('status', '!=', Statuses::LEAD_CHARGEBACK)->count(),
            'issued'     => (clone $base)->where('issuance_status', 'Issued')
                                ->where('status', '!=', Statuses::LEAD_CHARGEBACK)->count(),
            'not_issued' => (clone $base)->where('issuance_status', 'Not Issued')->count(),
            'chargeback' => (clone $base)->where('status', Statuses::LEAD_CHARGEBACK)->count(),
        ];

        $leads = (clone $base)
            ->with(['partner', 'carriers'])
            ->orderByDesc('created_at')
            ->paginate(30)
            ->withQueryString();

        return view('partner.sales-entry.submissions', compact('partner', 'leads', 'summary', 'search'));
    }

    /**
     * Only an active company or closer may submit; abort otherwise.
     */
    protected function authorizeSubmitter($partner): void
    {
        abort_unless(
            $partner && $partner->is_active && ($partner->isCcPartner() || $partner->isCloser()),
            403,
            'Your account is not permitted to submit sales.'
        );
    }
}
