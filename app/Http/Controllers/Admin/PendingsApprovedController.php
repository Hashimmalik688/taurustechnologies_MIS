<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgentCarrierState;
use App\Models\AuditLog;
use App\Models\InsuranceCarrier;
use App\Models\Lead;
use App\Models\Partner;
use App\Support\Statuses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Submissions
 *
 * Stage 2 in the sales pipeline.
 * Shows all leads validated as "valid" by Ravens Validator that have NOT yet been sent to
 * Pending Contract (pending_contract_at IS NULL).
 *
 * Manager actions:
 *   • Mark as Approved   — lead ready for policy submission
 *   • Mark as Declined   — reject the validated lead
 *   • Mark as Underwriting — send to underwriting
 *   • Send to Contract   — moves lead to Pending Contract
 *   • Mark Not Issued    — flags lead with a disposition for Retention to resolve
 *
 * Retention Officer actions:
 *   • Resolve Not Issued — clears the block; lead returns to this queue
 */
class PendingsApprovedController extends Controller
{
    public function index(Request $request)
    {
        $search   = $request->get('search');
        $carrier  = $request->get('carrier');
        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');
        $status   = $request->get('status', 'pending'); // Default to pending approval

        // Default to current month (same as Sales page)
        if (!$dateFrom && !$dateTo) {
            $dateFrom = now()->startOfMonth()->toDateString();
            $dateTo   = now()->endOfMonth()->toDateString();
        }

        // Base query builder for validated sales
        // For "approved" status: show sales that were approved (now in Pending Contracts)
        // For other statuses: show sales still awaiting decision (not in Pending Contracts)
        
        $baseConditions = function($query) {
            $query->where(function($q) {
                      // Unified: all teams use ravens_validation_status going forward
                      // OR validated_at as a fallback for legacy Peregrine leads
                      $q->where('ravens_validation_status', 'valid')
                        ->orWhereNotNull('validated_at');
                  })
                  ->whereNotNull('closer_name')
                  ->where('cn_name', '!=', '')
                  ->whereNotNull('cn_name')
                  ->where(function($q) {
                      $q->whereNotNull('sale_at')
                        ->orWhereNotNull('sale_date');
                  })
                  ->where(function($q) {
                      $q->where(function($sub) { $sub->whereNotNull('ssn')->where('ssn', '!=', ''); })
                        ->orWhere(function($sub) { $sub->whereNotNull('carrier_name')->where('carrier_name', '!=', ''); })
                        ->orWhere(function($sub) { $sub->whereNotNull('monthly_premium')->where('monthly_premium', '>', 0); });
                  });
        };

        $query = Lead::with(['insuranceCarrier', 'notIssuedBy', 'notIssuedResolvedBy', 'qaUser', 'submissionReviewer']);
        $baseConditions($query);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('cn_name',      'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('carrier_name', 'like', "%{$search}%")
                  ->orWhere('closer_name',  'like', "%{$search}%");
            });
        }

        if ($carrier) {
            $query->where('insurance_carrier_id', $carrier);
        }

        if ($dateFrom) {
            $query->whereDate('sale_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('sale_date', '<=', $dateTo);
        }

        // Status filter - approved shows sent-to-contract sales, others show pending sales
        if ($status === 'pending') {
            $query->whereNull('pending_contract_at')
                  ->where(function($q) {
                      $q->whereNull('submission_status')
                        ->orWhere('submission_status', 'pending');
                  });
        } elseif ($status === 'approved') {
            $query->where('submission_status', 'approved'); // Includes those sent to Pending Contracts
        } elseif ($status === 'declined') {
            $query->whereNull('pending_contract_at')->where('submission_status', 'declined');
        } elseif ($status === 'underwriting') {
            $query->whereNull('pending_contract_at')->where('submission_status', 'underwriting');
        }

        // Stats queries
        $statsBase = Lead::query();
        $baseConditions($statsBase);
        $statsBase->when($search, function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('cn_name', 'like', "%{$search}%")
                          ->orWhere('phone_number', 'like', "%{$search}%")
                          ->orWhere('carrier_name', 'like', "%{$search}%")
                          ->orWhere('closer_name', 'like', "%{$search}%");
                });
            })
            ->when($carrier, fn($q) => $q->where('insurance_carrier_id', $carrier))
            ->when($dateFrom, fn($q) => $q->whereDate('sale_date', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('sale_date', '<=', $dateTo));

        // Pending: NULL or 'pending', not sent to PC
        $pendingCount      = (clone $statsBase)->whereNull('pending_contract_at')
                              ->where(function($q) {
                                  $q->whereNull('submission_status')->orWhere('submission_status', 'pending');
                              })->count();
        $declinedCount     = (clone $statsBase)->whereNull('pending_contract_at')->where('submission_status', 'declined')->count();
        $underwritingCount = (clone $statsBase)->whereNull('pending_contract_at')->where('submission_status', 'underwriting')->count();
        // Approved: count all approved (they've been sent to Pending Contracts)
        $approvedCount     = (clone $statsBase)->where('submission_status', 'approved')->count();

        $leads    = $query->orderBy('sale_date', 'desc')->paginate(50);
        $carriers = InsuranceCarrier::where('is_active', true)
            ->whereHas('agentStates', fn($q) => $q->whereNotNull('partner_id'))
            ->orderBy('name')
            ->get(['id', 'name']);
        
        // Active partners for modal dropdown
        $partners = Partner::where('is_active', true)->orderBy('name')->get(['id', 'name', 'code']);
        
        // Build partner-carrier mapping for frontend validation
        $partnerCarriers = AgentCarrierState::whereNotNull('partner_id')
            ->select('partner_id', 'insurance_carrier_id')
            ->distinct()
            ->get()
            ->groupBy('partner_id')
            ->map(fn($states) => $states->pluck('insurance_carrier_id')->toArray())
            ->toArray();

        return view('admin.pendings-approved.index', compact(
            'leads', 'carriers', 'search', 'carrier',
            'dateFrom', 'dateTo', 'status',
            'pendingCount', 'approvedCount', 'declinedCount', 'underwritingCount',
            'partners', 'partnerCarriers'
        ));
    }

    /**
     * Send lead from Pendings Approved → Pending Contract.
     * Only allowed if lead is NOT currently blocked as Not Issued.
     */
    public function sendToContract(Request $request, int $id)
    {
        $lead = Lead::findOrFail($id);

        if (!empty($lead->pending_contract_at)) {
            return response()->json(['success' => false, 'message' => 'Lead has already been sent to Pending Contract.'], 422);
        }

        // Cannot send if there is an unresolved Not Issued block
        if (!empty($lead->not_issued_at) && empty($lead->not_issued_resolved_at)) {
            return response()->json(['success' => false, 'message' => 'Lead is blocked as Not Issued. Retention must resolve it first.'], 422);
        }

        $lead->pending_contract_at    = now();
        $lead->pending_contract_by_id = auth()->id();

        // If the lead was previously declined, reset the status so it no longer
        // shows as Declined on dashboards and the Sales Hub search.
        if ($lead->status === Statuses::LEAD_DECLINED) {
            $lead->status = Statuses::LEAD_ACCEPTED;
        }

        $lead->save();

        return response()->json([
            'success' => true,
            'message' => 'Lead sent to Pending Contract.',
        ]);
    }

    /**
     * Mark a lead as Not Issued (Manager action).
     * Sets the not_issued_disposition, not_issued_at, not_issued_by_id.
     * Clears any previous resolved state so it re-enters the Not Issued queue.
     */
    public function markNotIssued(Request $request, int $id)
    {
        $request->validate([
            'not_issued_disposition' => 'required|in:' . implode(',', array_keys(Statuses::NOT_ISSUED_DISPOSITIONS)),
        ]);

        $lead = Lead::findOrFail($id);

        if (!empty($lead->pending_contract_at)) {
            return response()->json(['success' => false, 'message' => 'Lead is already in Pending Contract.'], 422);
        }

        $lead->not_issued_disposition   = $request->not_issued_disposition;
        $lead->not_issued_at            = now();
        $lead->not_issued_by_id         = auth()->id();
        $lead->not_issued_resolved_at   = null;
        $lead->not_issued_resolved_by_id = null;
        $lead->save();

        return response()->json([
            'success' => true,
            'message' => 'Lead marked as Not Issued.',
            'disposition_label' => Statuses::NOT_ISSUED_DISPOSITIONS[$request->not_issued_disposition],
        ]);
    }

    /**
     * Retention Officer resolves a Not Issued block.
     * Clears not_issued fields so the lead goes back to the regular queue.
     */
    public function resolveNotIssued(Request $request, int $id)
    {
        $lead = Lead::findOrFail($id);

        if (empty($lead->not_issued_at)) {
            return response()->json(['success' => false, 'message' => 'Lead is not marked as Not Issued.'], 422);
        }

        $lead->not_issued_resolved_at    = now();
        $lead->not_issued_resolved_by_id = auth()->id();
        $lead->save();

        return response()->json([
            'success' => true,
            'message' => 'Not Issued block resolved. Lead is back in Submissions.',
        ]);
    }

    /**
     * Save lead details — Decision (submission_status), App ID, Policy Number, Partner.
     */
    public function saveDecision(Request $request, int $id)
    {
        $rules = [
            'submission_status'  => 'required|in:approved,declined,underwriting',
            'app_id'          => 'nullable|string|max:100',
            'policy_number'   => 'nullable|string|max:255',
            'assigned_partner'=> 'nullable|string|max:255',
            'partner_id'      => 'nullable|exists:partners,id',
        ];

        $request->validate($rules);

        $lead = Lead::findOrFail($id);

        // Save submission_status and reviewer info
        $lead->submission_status = $request->submission_status;
        $lead->submission_by = auth()->id();
        $lead->submission_at = now();

        // Save App ID for all decisions
        if ($request->filled('app_id')) $lead->app_id = $request->app_id;

        // For approved sales: save optional fields and auto-send to Pending Contracts
        if ($request->submission_status === 'approved') {
            if ($request->filled('policy_number'))    $lead->policy_number    = $request->policy_number;
            if ($request->filled('assigned_partner')) $lead->assigned_partner = $request->assigned_partner;
            
            // Validate partner has access to this carrier
            if ($request->partner_id) {
                $carrierId = $lead->insurance_carrier_id;
                
                // Check if carrier is assigned - only validate if carrier is set
                if ($carrierId) {
                    $partnerHasCarrier = AgentCarrierState::where('partner_id', $request->partner_id)
                        ->where('insurance_carrier_id', $carrierId)
                        ->exists();
                    
                    if (!$partnerHasCarrier) {
                        $partner = Partner::find($request->partner_id);
                        $carrier = InsuranceCarrier::find($carrierId);
                        
                        return response()->json([
                            'success' => false,
                            'message' => sprintf(
                                'Partner "%s" does not have access to carrier "%s". Please assign this carrier to the partner first or select a different partner.',
                                $partner->name ?? 'Unknown',
                                $carrier->name ?? 'Unknown'
                            ),
                        ], 422);
                    }
                }
                
                $lead->partner_id     = $request->partner_id;
                $lead->partner_set_at = now();
            }

            // Auto-send approved sales to Pending Contracts
            $lead->pending_contract_at    = now();
            $lead->pending_contract_by_id = auth()->id();

            // Reset main status so it no longer appears as declined on closer's dashboard
            $lead->status = Statuses::LEAD_ACCEPTED;
        } elseif ($request->submission_status === 'declined') {
            // Mirror the status field so the closer's dashboard reflects the decline
            $lead->status      = Statuses::LEAD_DECLINED;
            $lead->declined_at = now();
        }

        $lead->save();

        $msg = 'Decision saved: ' . ucfirst($request->submission_status);
        if ($request->submission_status === 'approved') {
            $msg .= ' — Sent to Pending Contracts';
        }

        return response()->json([
            'success' => true,
            'message' => $msg,
        ]);
    }

    // Legacy updateStatus removed — submission_status no longer used in flow

    /**
     * Update a single field on a lead
     */
    public function updateField(Request $request, int $id)
    {
        $request->validate([
            'field' => 'required|in:policy_number,assigned_partner,app_id',
            'value' => 'nullable|string|max:255',
        ]);

        $lead = Lead::findOrFail($id);
        $lead->{$request->field} = $request->value;
        $lead->save();

        return response()->json(['success' => true, 'message' => 'Field updated.']);
    }

    /**
     * Update coverage_amount, monthly_premium, policy_type
     */
    public function updateCoverage(Request $request, int $id)
    {
        $request->validate([
            'coverage_amount'      => 'nullable|numeric|min:0',
            'monthly_premium'      => 'nullable|numeric|min:0',
            'policy_type'          => 'nullable|string|max:255',
            'insurance_carrier_id' => 'nullable|integer|exists:insurance_carriers,id',
            'initial_draft_date'   => 'nullable|date',
            'future_draft_date'    => 'nullable|date',
        ]);

        $lead = Lead::findOrFail($id);
        if ($request->has('coverage_amount'))      $lead->coverage_amount      = $request->coverage_amount;
        if ($request->has('monthly_premium'))      $lead->monthly_premium      = $request->monthly_premium;
        if ($request->has('policy_type'))          $lead->policy_type          = $request->policy_type;
        if ($request->has('insurance_carrier_id') && $request->insurance_carrier_id) {
            $lead->insurance_carrier_id = $request->insurance_carrier_id;
            // Sync carrier_name only when a valid carrier ID is provided
            // Never clear carrier_name — it may reference a carrier not yet in the insurance_carriers table
            $carrier = InsuranceCarrier::find((int) $request->insurance_carrier_id);
            if ($carrier) {
                $lead->carrier_name = $carrier->name;
            }
        }
        if ($request->has('initial_draft_date'))   $lead->initial_draft_date   = $request->initial_draft_date;
        if ($request->has('future_draft_date'))    $lead->future_draft_date    = $request->future_draft_date;
        $lead->save();

        return response()->json(['success' => true, 'message' => 'Coverage details updated.']);
    }

    /**
     * Export current filtered view as a plain printable corporate report.
     * Opens in a new tab — all filters honoured, no pagination.
     */
    public function export(Request $request)
    {
        $search   = $request->get('search');
        $carrier  = $request->get('carrier');
        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');
        $status   = $request->get('status', 'pending');

        if (!$dateFrom && !$dateTo) {
            $dateFrom = now()->startOfMonth()->toDateString();
            $dateTo   = now()->endOfMonth()->toDateString();
        }

        $query = Lead::with(['insuranceCarrier', 'submissionReviewer'])
            ->where(function($q) {
                // Unified: all teams use ravens_validation_status going forward
                // OR validated_at as a fallback for legacy Peregrine leads
                $q->where('ravens_validation_status', 'valid')
                  ->orWhereNotNull('validated_at');
            })
            ->whereNotNull('closer_name')
            ->where('cn_name', '!=', '')
            ->whereNotNull('cn_name')
            ->where(function ($q) {
                $q->whereNotNull('sale_at')->orWhereNotNull('sale_date');
            })
            ->where(function ($q) {
                $q->where(function ($s) { $s->whereNotNull('ssn')->where('ssn', '!=', ''); })
                  ->orWhere(function ($s) { $s->whereNotNull('carrier_name')->where('carrier_name', '!=', ''); })
                  ->orWhere(function ($s) { $s->whereNotNull('monthly_premium')->where('monthly_premium', '>', 0); });
            });

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('cn_name',      'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('carrier_name', 'like', "%{$search}%")
                  ->orWhere('closer_name',  'like', "%{$search}%");
            });
        }

        if ($carrier) $query->where('insurance_carrier_id', $carrier);
        if ($dateFrom) $query->whereDate('sale_date', '>=', $dateFrom);
        if ($dateTo)   $query->whereDate('sale_date', '<=', $dateTo);

        if ($status === 'pending') {
            $query->whereNull('pending_contract_at')
                  ->where(function ($q) { $q->whereNull('submission_status')->orWhere('submission_status', 'pending'); });
        } elseif ($status === 'approved') {
            $query->where('submission_status', 'approved');
        } elseif ($status === 'declined') {
            $query->whereNull('pending_contract_at')->where('submission_status', 'declined');
        } elseif ($status === 'underwriting') {
            $query->whereNull('pending_contract_at')->where('submission_status', 'underwriting');
        }

        $leads = $query->orderBy('sale_date', 'desc')->get();

        $stateCounts = $leads
            ->groupBy(fn ($l) => strtoupper(trim($l->state ?: '')) ?: 'Unknown')
            ->map->count()
            ->sortDesc();

        $statusLabel = match($status) {
            'approved'     => 'Approved',
            'declined'     => 'Declined',
            'underwriting' => 'Underwriting',
            default        => 'Pending Approval',
        };
        $exportedAt = now()->format('F j, Y  g:i A');

        return view('admin.pendings-approved.export', compact(
            'leads', 'stateCounts', 'status', 'statusLabel',
            'dateFrom', 'dateTo', 'search', 'exportedAt'
        ));
    }
}
