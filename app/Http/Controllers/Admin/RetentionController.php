<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Lead;
use App\Models\LeadFieldHighlight;
use App\Support\Statuses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RetentionController extends Controller
{
    /**
     * Display retention management page (for admins/managers)
     */
    public function index(Request $request)
    {
        // Show all leads by default (no date filter pre-applied)
        $search      = $request->get('search');
        $month       = $request->get('month');
        $year        = $request->get('year');
        $date_from   = $request->get('date_from');
        $date_to     = $request->get('date_to');
        $disp_filter = $request->get('disp_filter'); // filter by specific retention disposition (for KPI click)
        // If a specifically *disposed* disposition is clicked, auto-enable disposed view.
        // Contact-attempt dispositions (in_progress, not_answering, unable_to_connect) are NOT disposed —
        // they stay in the active list even when filtered.
        $disposed = ($disp_filter && in_array($disp_filter, Statuses::RETENTION_DISPOSED_STATUSES))
            ? true
            : $request->boolean('disposed', false);

        $applyFilters = function($query) use ($search, $month, $year, $date_from, $date_to) {
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('cn_name',      'like', "%{$search}%")
                      ->orWhere('phone_number','like', "%{$search}%")
                      ->orWhere('carrier_name','like', "%{$search}%")
                      ->orWhere('closer_name', 'like', "%{$search}%");
                });
            }
            if ($date_from) $query->whereDate('sale_date', '>=', $date_from);
            if ($date_to)   $query->whereDate('sale_date', '<=', $date_to);
            if (!$date_from && !$date_to) {
                if ($month && $year) {
                    $query->whereMonth('sale_date', $month)->whereYear('sale_date', $year);
                } elseif ($year) {
                    $query->whereYear('sale_date', $year);
                }
            }
            return $query;
        };

        // For Not Paid leads, filter by not_paid_at date (when the lead was flagged),
        // not sale_date — so a lead sold in a prior month still appears in the retention queue.
        $applyNpFilters = function($query) use ($search, $month, $year, $date_from, $date_to) {
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('cn_name',      'like', "%{$search}%")
                      ->orWhere('phone_number','like', "%{$search}%")
                      ->orWhere('carrier_name','like', "%{$search}%")
                      ->orWhere('closer_name', 'like', "%{$search}%");
                });
            }
            if ($date_from) $query->whereDate('not_paid_at', '>=', $date_from);
            if ($date_to)   $query->whereDate('not_paid_at', '<=', $date_to);
            if (!$date_from && !$date_to) {
                if ($month && $year) {
                    $query->whereMonth('not_paid_at', $month)->whereYear('not_paid_at', $year);
                } elseif ($year) {
                    $query->whereYear('not_paid_at', $year);
                }
            }
            return $query;
        };

        // Disposed = all retention_disposition values except 'pending'
        $disposedStatuses = Statuses::RETENTION_DISPOSED_STATUSES;

        // NOT ISSUED base scope
        $niBase = Lead::whereNotNull('not_issued_at')
                      ->whereNull('not_issued_resolved_at')
                      ->whereNotNull('pending_contract_at');

        // NOT PAID base scope
        // Allow chargeback leads sent to retention (paid_at is set but cb_sent_to_retention_at overrides)
        $npBase = Lead::whereNotNull('not_paid_at')
                      ->where(fn($q) => $q->whereNull('paid_at')->orWhereNotNull('cb_sent_to_retention_at'))
                      ->whereNull('policy_died_at');

        // CANCELLED BY CUSTOMER base scope (subset of Not Issued, separated into own tab)
        $cancelledBase = Lead::whereNotNull('not_issued_at')
                             ->whereNull('not_issued_resolved_at')
                             ->whereNotNull('pending_contract_at')
                             ->where('not_issued_disposition', Statuses::NI_CANCELLED_BY_CUSTOMER);

        // ----- KPI counts (all time, across both scopes) -----
        // A lead is "recalled" if recall_requested_at is set OR ret_action_status = 'recalled'.
        // A lead is "pending" only when it has no ret_action_status (or = 'pending') AND is NOT recalled.
        $kpi = [];
        foreach (array_keys(Statuses::RETENTION_DISPOSITIONS) as $s) {
            if ($s === 'pending') {
                $kpi[$s] = $niBase->clone()->where(fn($q) => $q->whereNull('retention_disposition')->orWhere('retention_disposition', 'pending'))->count()
                         + $npBase->clone()->where(fn($q) => $q->whereNull('retention_disposition')->orWhere('retention_disposition', 'pending'))->count();
            } else {
                $kpi[$s] = $niBase->clone()->where('retention_disposition', $s)->count()
                         + $npBase->clone()->where('retention_disposition', $s)->count();
            }
        }

        // ----- Active counts (filter for the counts shown on the tabs) -----
        // Apply disp_filter here too so tab badges reflect what's actually in each tab for the current KPI selection.
        $buildDispCondition = function($query) use ($disp_filter, $disposed, $disposedStatuses) {
            if ($disp_filter) {
                if ($disp_filter === 'pending') {
                    $query->where(fn($q) => $q->whereNull('retention_disposition')->orWhere('retention_disposition', 'pending'));
                } else {
                    $query->where('retention_disposition', $disp_filter);
                }
            } elseif ($disposed) {
                $query->whereIn('retention_disposition', $disposedStatuses);
            } else {
                $query->where(fn($q) => $q->whereNull('retention_disposition')->orWhereNotIn('retention_disposition', $disposedStatuses));
            }
            return $query;
        };

        $not_issued_count = $buildDispCondition(
            $applyFilters($niBase->clone())->where('not_issued_disposition', '!=', Statuses::NI_CANCELLED_BY_CUSTOMER)
        )->count();
        $not_paid_count   = $buildDispCondition(
            $applyNpFilters($npBase->clone())
        )->count();
        $cancelled_count  = $buildDispCondition(
            $applyFilters($cancelledBase->clone())
        )->count();

        // ----- Table queries -----
        if ($disposed) {
            // Show disposed leads
            $ni_query = Lead::with(['insuranceCarrier', 'notIssuedBy', 'retActionUpdatedBy', 'recallRequestedBy', 'fieldHighlights', 'partner'])
                ->whereNotNull('not_issued_at')
                ->whereNull('not_issued_resolved_at')
                ->whereNotNull('pending_contract_at')
                ->where('not_issued_disposition', '!=', Statuses::NI_CANCELLED_BY_CUSTOMER)
                ->whereIn('retention_disposition', $disposedStatuses);
            $np_query = Lead::with(['insuranceCarrier', 'notPaidBy', 'retActionUpdatedBy', 'recallRequestedBy', 'fieldHighlights', 'partner'])
                ->whereNotNull('not_paid_at')
                ->where(fn($q) => $q->whereNull('paid_at')->orWhereNotNull('cb_sent_to_retention_at'))
                ->whereNull('policy_died_at')
                ->whereIn('retention_disposition', $disposedStatuses);
            $cancelled_query = Lead::with(['insuranceCarrier', 'notIssuedBy', 'retActionUpdatedBy', 'recallRequestedBy', 'fieldHighlights', 'partner'])
                ->whereNotNull('not_issued_at')
                ->whereNull('not_issued_resolved_at')
                ->whereNotNull('pending_contract_at')
                ->where('not_issued_disposition', Statuses::NI_CANCELLED_BY_CUSTOMER)
                ->whereIn('retention_disposition', $disposedStatuses);
        } else {
            // Show active (non-disposed) leads
            $ni_query = Lead::with(['insuranceCarrier', 'notIssuedBy', 'retActionUpdatedBy', 'recallRequestedBy', 'fieldHighlights', 'partner'])
                ->whereNotNull('not_issued_at')
                ->whereNull('not_issued_resolved_at')
                ->whereNotNull('pending_contract_at')
                ->where('not_issued_disposition', '!=', Statuses::NI_CANCELLED_BY_CUSTOMER)
                ->where(fn($q) => $q->whereNull('retention_disposition')->orWhereNotIn('retention_disposition', $disposedStatuses));
            $np_query = Lead::with(['insuranceCarrier', 'notPaidBy', 'retActionUpdatedBy', 'recallRequestedBy', 'fieldHighlights', 'partner'])
                ->whereNotNull('not_paid_at')
                ->where(fn($q) => $q->whereNull('paid_at')->orWhereNotNull('cb_sent_to_retention_at'))
                ->whereNull('policy_died_at')
                ->where(fn($q) => $q->whereNull('retention_disposition')->orWhereNotIn('retention_disposition', $disposedStatuses));
            $cancelled_query = Lead::with(['insuranceCarrier', 'notIssuedBy', 'retActionUpdatedBy', 'recallRequestedBy', 'fieldHighlights', 'partner'])
                ->whereNotNull('not_issued_at')
                ->whereNull('not_issued_resolved_at')
                ->whereNotNull('pending_contract_at')
                ->where('not_issued_disposition', Statuses::NI_CANCELLED_BY_CUSTOMER)
                ->where(fn($q) => $q->whereNull('retention_disposition')->orWhereNotIn('retention_disposition', $disposedStatuses));
        }
        // Further narrow by specific disposition if a KPI pill was clicked
        if ($disp_filter) {
            if ($disp_filter === 'pending') {
                $ni_query->where(fn($q) => $q->whereNull('retention_disposition')->orWhere('retention_disposition', 'pending'));
                $np_query->where(fn($q) => $q->whereNull('retention_disposition')->orWhere('retention_disposition', 'pending'));
                $cancelled_query->where(fn($q) => $q->whereNull('retention_disposition')->orWhere('retention_disposition', 'pending'));
            } else {
                $ni_query->where('retention_disposition', $disp_filter);
                $np_query->where('retention_disposition', $disp_filter);
                $cancelled_query->where('retention_disposition', $disp_filter);
            }
        }

        $ni_query        = $applyFilters($ni_query);
        $np_query        = $applyNpFilters($np_query);
        $cancelled_query = $applyFilters($cancelled_query);

        $not_issued_leads = $ni_query->latest('not_issued_at')->paginate(50, ['*'], 'not_issued_page');
        $not_paid_leads   = $np_query->latest('not_paid_at')->paginate(50, ['*'], 'not_paid_page');
        $cancelled_leads  = $cancelled_query->latest('not_issued_at')->paginate(50, ['*'], 'cancelled_page');

        // Auto-activate the first tab that has results when a KPI filter is applied
        if ($disp_filter && $not_issued_leads->isEmpty() && !$not_paid_leads->isEmpty()) {
            $activeTab = 'not-paid';
        } elseif ($disp_filter && $not_issued_leads->isEmpty() && $not_paid_leads->isEmpty() && !$cancelled_leads->isEmpty()) {
            $activeTab = 'cancelled';
        } else {
            $activeTab = 'not-issued';
        }

        $hasZoomToken = \App\Models\ZoomToken::where('user_id', Auth::id())
            ->where('expires_at', '>', now())
            ->exists();

        $retentionDispositions = Statuses::RETENTION_DISPOSITIONS;

        return view('admin.retention.index', compact(
            'not_issued_leads',
            'not_paid_leads',
            'cancelled_leads',
            'not_issued_count',
            'not_paid_count',
            'cancelled_count',
            'kpi',
            'disposed',
            'disp_filter',
            'activeTab',
            'retentionDispositions',
            'search',
            'month',
            'year',
            'date_from',
            'date_to'
        ));
    }

    /**
     * Update retention action status (disposed/active sub-status).
     */
    public function updateActionStatus(Request $request, int $id)
    {
        $request->validate([
            'ret_action_status' => 'required|in:pending,in_progress,waiting_on_cx,fixed,cancelled,recalled',
        ]);

        $lead = Lead::findOrFail($id);
        $old  = $lead->ret_action_status;

        $lead->ret_action_status      = $request->ret_action_status;
        $lead->ret_action_updated_at  = now();
        $lead->ret_action_updated_by  = Auth::id();

        // If recalled via this method, also set recall fields if not already set
        if ($request->ret_action_status === 'recalled' && !$lead->recall_requested_at) {
            $lead->recall_requested_at = now();
            $lead->recall_requested_by = Auth::id();
            $lead->recall_note         = $request->input('note', 'Recalled from Retention Management');
        }

        $lead->save();

        AuditLog::create([
            'user_id'    => Auth::id(),
            'action'     => 'Retention — Update Action Status',
            'model'      => 'Lead',
            'model_id'   => $lead->id,
            'old_values' => json_encode(['ret_action_status' => $old]),
            'new_values' => json_encode(['ret_action_status' => $request->ret_action_status]),
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Status updated to \"{$request->ret_action_status}\".",
            'disposed' => in_array($request->ret_action_status, Statuses::RET_DISPOSED_STATUSES),
        ]);
    }

    /**
     * Update retention status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,retained,rewrite'
        ]);

        $lead = Lead::findOrFail($id);
        
        // Get the retention officer's name
        $retentionOfficer = auth()->user()->name;

        // Update retention status
        $lead->retention_status = $request->status;

        // Update based on retention status
        if ($request->status == Statuses::RETENTION_RETAINED) {
            // When a chargeback is retained (< 30 days), create a NEW sale
            // Retention officer successfully re-sold the policy
            
            // Create a new sale record (duplicate of the chargeback but as new sale)
            $newSale = $lead->replicate();
            $newSale->closer_name = $retentionOfficer; // Retention officer becomes the closer
            $newSale->sale_at = now();
            $newSale->sale_date = now()->format('Y-m-d');
            $newSale->status = Statuses::LEAD_PENDING; // New sale goes through approval process
            $newSale->retention_status = null; // Clear retention status for new sale
            $newSale->is_rewrite = false;
            $newSale->chargeback_marked_date = null; // Not a chargeback anymore
            $newSale->qa_status = Statuses::QA_PENDING; // Reset QA status
            $newSale->qa_reason = null;
            $newSale->qa_user_id = null;
            $newSale->submission_status = Statuses::SUB_PENDING; // Reset manager status
            $newSale->submission_reason = null;
            $newSale->submission_by = null;
            $newSale->comments = 'Retained from chargeback by ' . $retentionOfficer;
            $newSale->save();
            
            // Mark the original chargeback as retained
            $lead->status = Statuses::LEAD_ACCEPTED;
            $lead->retained_at = now();
            $lead->retention_officer_id = auth()->id();
        } elseif ($request->status == Statuses::RETENTION_REWRITE) {
            // When marked as rewrite (>= 30 days), create a NEW sale
            // This is essentially a new policy since it's been too long
            
            // Create a new sale record
            $newSale = $lead->replicate();
            $newSale->closer_name = $retentionOfficer; // Retention officer becomes the closer
            $newSale->sale_at = now();
            $newSale->sale_date = now()->format('Y-m-d');
            $newSale->status = Statuses::LEAD_PENDING; // New sale goes through approval process
            $newSale->retention_status = null; // Clear retention status for new sale
            $newSale->is_rewrite = false; // This is a fresh sale now
            $newSale->chargeback_marked_date = null; // Not a chargeback anymore
            $newSale->qa_status = Statuses::QA_PENDING; // Reset QA status
            $newSale->qa_reason = null;
            $newSale->qa_user_id = null;
            $newSale->submission_status = Statuses::SUB_PENDING; // Reset manager status
            $newSale->submission_reason = null;
            $newSale->submission_by = null;
            $newSale->comments = 'Rewritten from chargeback by ' . $retentionOfficer;
            $newSale->rewrite_source_lead_id = $lead->id; // track which original lead spawned this sale
            $newSale->rewrite_sent_back_at = null;
            $newSale->save();
            
            // Mark the original as rewrite
            $lead->is_rewrite = true;
            $lead->retained_at = null;
            $lead->retention_officer_id = auth()->id();
        } elseif ($request->status == Statuses::RETENTION_PENDING) {
            // When marked as pending/yet to retain, clear flags
            $lead->is_rewrite = false;
            $lead->retained_at = null;
        }

        $lead->save();

        return response()->json([
            'success' => true,
            'message' => 'Retention status updated successfully. New sale created.'
        ]);
    }

    /**
     * Display incomplete issuance list (incomplete issuances sent to retention)
     * NOTE: This is now integrated into the main retention.index view as "Disposition" tab
     * Keeping this method for backwards compatibility if needed
     */
    public function incompleteIssuance(Request $request)
    {
        $query = Lead::with('insuranceCarrier')
            ->whereNotNull('closer_name')
            ->whereNotNull('sale_at')
            ->where('submission_status', Statuses::SUB_APPROVED)
            ->where('issuance_status', 'Incomplete');
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('cn_name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('carrier_name', 'like', "%{$search}%")
                  ->orWhere('closer_name', 'like', "%{$search}%");
            });
        }
        
        // Filter by carrier
        if ($request->filled('carrier')) {
            $query->where('carrier_name', $request->carrier);
        }
        
        // Filter by disposition status
        if ($request->filled('disposition')) {
            $query->where('issuance_disposition', $request->disposition);
        }
        
        // Filter by policy type
        if ($request->filled('policy_type')) {
            $query->where('policy_type', $request->policy_type);
        }
        
        // Month filter for sale_date
        if ($request->filled('month')) {
            $query->whereMonth('sale_date', $request->month);
        }
        if ($request->filled('year')) {
            $query->whereYear('sale_date', $request->year);
        }
        
        // Get unique carriers for filter dropdown
        $carriers = Lead::distinct()->pluck('carrier_name')->filter();
        
        $leads = $query->orderBy('sale_date', 'desc')->paginate(50);
        return view('admin.retention.incomplete', compact('leads', 'carriers'));
    }

    /**
     * Show full details of incomplete issuance lead
     */
    public function showIncompleteDetails($id)
    {
        $lead = Lead::with('insuranceCarrier')->findOrFail($id);
        
        if ($lead->issuance_status !== 'Incomplete') {
            return redirect()->route('retention.incomplete')->with('error', 'This lead is not marked as incomplete issuance.');
        }
        
        return view('admin.retention.incomplete-details', compact('lead'));
    }

    /**
     * Save disposition for incomplete issuance
     */
    public function saveDisposition(Request $request, $id)
    {
        $request->validate([
            'issuance_disposition' => 'required|in:Via Portal,Via Email,By Carrier,By Bank',
            'issuance_reason' => 'nullable|string|max:1000'
        ]);

        $lead = Lead::findOrFail($id);
        
        if ($lead->issuance_status !== 'Incomplete') {
            return response()->json([
                'success' => false,
                'message' => 'This lead is not marked as incomplete issuance.'
            ], 422);
        }
        
        $lead->issuance_disposition = $request->issuance_disposition;
        $lead->issuance_reason = $request->issuance_reason;
        $lead->disposition_officer_id = auth()->id();
        $lead->issuance_disposition_date = now();
        
        // Check for other insurances if applicable
        if (in_array($request->issuance_disposition, ['By Carrier', 'By Bank'])) {
            $otherInsurances = $this->checkOtherInsurancesCount($lead, $request->issuance_disposition);
            $lead->has_other_insurances = $otherInsurances > 0;
        }
        
        $lead->save();

        return response()->json([
            'success' => true,
            'message' => 'Disposition saved successfully'
        ]);
    }

    /**
     * Check for other insurances for a lead (AJAX endpoint)
     */
    public function checkOtherInsurances(Request $request, $id)
    {
        $request->validate([
            'disposition' => 'required|in:By Carrier,By Bank'
        ]);

        $lead = Lead::findOrFail($id);
        $otherInsurances = [];
        
        if ($request->disposition === 'By Carrier') {
            // Check for other insurances with the same carrier
            $otherInsurances = Lead::where('phone_number', $lead->phone_number)
                ->where('carrier_name', $lead->carrier_name)
                ->where('id', '!=', $id)
                ->where(function($q) {
                    $q->where('status', Statuses::LEAD_ACCEPTED)
                      ->orWhere('status', 'verified')
                      ->orWhere('status', Statuses::LEAD_CLOSED);
                })
                ->select('id', 'cn_name', 'carrier_name', 'policy_type', 'sale_date', 'policy_number')
                ->get();
        } elseif ($request->disposition === 'By Bank') {
            // Check for other insurances with the same bank account
            $otherInsurances = Lead::where('account_number', $lead->account_number)
                ->where('id', '!=', $id)
                ->where(function($q) {
                    $q->where('status', Statuses::LEAD_ACCEPTED)
                      ->orWhere('status', 'verified')
                      ->orWhere('status', Statuses::LEAD_CLOSED);
                })
                ->select('id', 'cn_name', 'carrier_name', 'policy_type', 'sale_date', 'policy_number', 'bank_name')
                ->get();
        }

        return response()->json([
            'count' => $otherInsurances->count(),
            'insurances' => $otherInsurances
        ]);
    }

    /**
     * Helper method to count other insurances
     */
    private function checkOtherInsurancesCount($lead, $disposition)
    {
        if ($disposition === 'By Carrier') {
            return Lead::where('phone_number', $lead->phone_number)
                ->where('carrier_name', $lead->carrier_name)
                ->where('id', '!=', $lead->id)
                ->where(function($q) {
                    $q->where('status', Statuses::LEAD_ACCEPTED)
                      ->orWhere('status', 'verified')
                      ->orWhere('status', Statuses::LEAD_CLOSED);
                })
                ->count();
        } elseif ($disposition === 'By Bank') {
            return Lead::where('account_number', $lead->account_number)
                ->where('id', '!=', $lead->id)
                ->where(function($q) {
                    $q->where('status', Statuses::LEAD_ACCEPTED)
                      ->orWhere('status', 'verified')
                      ->orWhere('status', Statuses::LEAD_CLOSED);
                })
                ->count();
        }
        
        return 0;
    }

    /**
     * Update all editable lead fields from the Retention Management modal.
     * Detects changed fields, saves them to lead_field_highlights for cross-page badges.
     */
    public function update(Request $request, int $id)
    {
        $lead = Lead::findOrFail($id);

        $fillable = [
            'cn_name', 'phone_number', 'secondary_phone_number', 'date_of_birth',
            'age', 'gender', 'ssn', 'address', 'state', 'zip_code',
            'policy_type', 'policy_number', 'carrier_name', 'coverage_amount',
            'monthly_premium', 'initial_draft_date', 'future_draft_date',
            'closer_name', 'sale_date',
            'smoker', 'height', 'weight', 'medical_issue', 'medications',
            'doctor_name', 'doctor_number', 'doctor_address',
            'bank_name', 'account_type', 'account_title', 'routing_number',
            'account_number', 'bank_balance', 'ss_amount', 'ss_date',
            'bank_verification_status', 'card_number', 'cvv', 'expiry_date',
            'beneficiaries', 'staff_notes', 'comments', 'retention_notes',
        ];

        $request->validate([
            'cn_name'       => 'nullable|string|max:255',
            'phone_number'  => 'nullable|string|max:30',
            'coverage_amount'  => 'nullable|numeric|min:0',
            'monthly_premium'  => 'nullable|numeric|min:0',
            'bank_balance'     => 'nullable|numeric|min:0',
            'ss_amount'        => 'nullable|numeric|min:0',
            'beneficiaries'    => 'nullable|json',
            'smoker'           => 'nullable|boolean',
        ]);

        $changedFields = [];
        $oldValues     = [];
        $newValues     = [];

        foreach ($fillable as $field) {
            if (!$request->has($field)) continue;

            $newVal = $request->input($field);
            $oldVal = $lead->{$field};

            // Normalize for comparison (cast to string)
            $oldStr = is_null($oldVal) ? '' : (string) $oldVal;
            $newStr = is_null($newVal) ? '' : (string) $newVal;

            if ($oldStr !== $newStr) {
                $changedFields[]       = $field;
                $oldValues[$field]     = $oldVal;
                $newValues[$field]     = $newVal;
                $lead->{$field}        = $newVal;
            }
        }

        if (empty($changedFields)) {
            return response()->json(['success' => true, 'message' => 'No changes detected.', 'changed' => []]);
        }

        $lead->save();

        // Upsert highlight records
        $now = now();
        foreach ($changedFields as $field) {
            LeadFieldHighlight::updateOrCreate(
                ['lead_id' => $lead->id, 'field_name' => $field],
                ['updated_by_id' => Auth::id(), 'updated_at' => $now]
            );
        }

        AuditLog::create([
            'user_id'    => Auth::id(),
            'action'     => 'Retention — Edit Lead Fields',
            'model'      => 'Lead',
            'model_id'   => $lead->id,
            'old_values' => json_encode($oldValues),
            'new_values' => json_encode($newValues),
            'ip_address' => $request->ip(),
        ]);

        // Return highlights map: field => {by, at}
        $highlightsMap = [];
        foreach ($changedFields as $field) {
            $highlightsMap[$field] = [
                'by' => Auth::user()->name,
                'at' => $now->format('m/d/Y h:i A'),
            ];
        }

        return response()->json([
            'success'    => true,
            'message'    => count($changedFields) . ' field(s) updated.',
            'changed'    => $changedFields,
            'highlights' => $highlightsMap,
        ]);
    }

    /**
     * Set the retention disposition for a lead.
     * Replaces the old ret_action_status workflow in the UI.
     */
    public function setDisposition(Request $request, int $id)
    {
        $validDispositions = array_keys(Statuses::RETENTION_DISPOSITIONS);

        $request->validate([
            'disposition' => 'required|in:' . implode(',', $validDispositions),
            'recall_note' => 'required_if:disposition,recalled_to_closer|nullable|string|max:1000',
        ]);

        $lead = Lead::findOrFail($id);
        $old  = $lead->retention_disposition;

        $lead->retention_disposition = $request->disposition;

        // When recalled to closer, also populate the recall fields
        if ($request->disposition === Statuses::RET_DISP_RECALLED_TO_CLOSER) {
            $lead->recall_requested_at = now();
            $lead->recall_requested_by = Auth::id();
            $lead->recall_note         = $request->recall_note;
            $lead->ret_action_status   = 'recalled'; // keep legacy field in sync
        }

        $lead->ret_action_updated_at = now();
        $lead->ret_action_updated_by = Auth::id();
        $lead->save();

        AuditLog::create([
            'user_id'    => Auth::id(),
            'action'     => 'Retention — Set Disposition',
            'model'      => 'Lead',
            'model_id'   => $lead->id,
            'old_values' => json_encode(['retention_disposition' => $old]),
            'new_values' => json_encode(['retention_disposition' => $request->disposition]),
            'ip_address' => $request->ip(),
        ]);

        $label    = Statuses::RETENTION_DISPOSITIONS[$request->disposition] ?? $request->disposition;
        $disposed = in_array($request->disposition, Statuses::RETENTION_DISPOSED_STATUSES);

        return response()->json([
            'success'     => true,
            'message'     => "Disposition set to \"$label\".",
            'disposition' => $request->disposition,
            'label'       => $label,
            'disposed'    => $disposed,
        ]);
    }

    /**
     * Revert a "Rewrite" lead back to the retention queue.
     * - Resets the original lead's retention_disposition to 'pending' so it re-appears in the active list.
     * - Marks the rewrite sale (the new sale that was created) as "sent back" to hide it from sales views.
     */
    public function sendBackFromRewrite(Request $request, int $id)
    {
        $lead = Lead::findOrFail($id);

        if ($lead->retention_disposition !== Statuses::RET_DISP_REWRITE) {
            return response()->json([
                'success' => false,
                'message' => 'This lead is not marked as Rewrite.',
            ], 422);
        }

        // Reset the original lead back to active retention queue
        $old = $lead->retention_disposition;
        $lead->retention_disposition    = Statuses::RET_DISP_PENDING;
        $lead->is_rewrite               = false;
        $lead->ret_action_updated_at    = now();
        $lead->ret_action_updated_by    = Auth::id();
        $lead->save();

        // Mark the associated rewrite sale so it hides from sales views
        $rewriteSale = Lead::where('rewrite_source_lead_id', $lead->id)
            ->whereNull('rewrite_sent_back_at')
            ->latest('created_at')
            ->first();

        if ($rewriteSale) {
            $rewriteSale->rewrite_sent_back_at = now();
            $rewriteSale->save();
        }

        AuditLog::create([
            'user_id'    => Auth::id(),
            'action'     => 'Retention — Send Back from Rewrite',
            'model'      => 'Lead',
            'model_id'   => $lead->id,
            'old_values' => json_encode(['retention_disposition' => $old, 'is_rewrite' => true]),
            'new_values' => json_encode(['retention_disposition' => Statuses::RET_DISP_PENDING, 'is_rewrite' => false]),
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Lead \"{$lead->cn_name}\" sent back to retention queue.",
        ]);
    }

    /**
     * Recall / Send Back a lead to the closer for re-dial.
     * Sets recall fields so the closer sees it on their Ravens dashboard.
     */
    public function recallToCloser(Request $request, int $id)
    {
        $request->validate([
            'recall_note' => 'required|string|max:1000',
        ]);

        $lead = Lead::findOrFail($id);

        if ($lead->recall_requested_at) {
            return response()->json([
                'success' => false,
                'message' => 'This lead has already been recalled.',
            ], 422);
        }

        $lead->recall_requested_at = now();
        $lead->recall_requested_by = Auth::id();
        $lead->recall_note         = $request->recall_note;
        $lead->ret_action_status   = 'recalled';
        $lead->save();

        AuditLog::create([
            'user_id'    => Auth::id(),
            'action'     => 'Retention — Recall to Closer',
            'model'      => 'Lead',
            'model_id'   => $lead->id,
            'old_values' => json_encode(['recall_requested_at' => null]),
            'new_values' => json_encode([
                'recall_requested_at' => now()->toISOString(),
                'recall_note'         => $request->recall_note,
            ]),
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Lead \"{$lead->cn_name}\" recalled to closer.",
        ]);
    }
}
