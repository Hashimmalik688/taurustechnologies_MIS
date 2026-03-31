<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Lead;
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
        // Default to current month/year if no date filter is applied
        if (!$request->filled('month') && !$request->filled('year')
            && !$request->filled('date_from') && !$request->filled('date_to')) {
            $request->merge([
                'month' => now()->month,
                'year'  => now()->year,
            ]);
        }

        $search    = $request->get('search');
        $month     = $request->get('month');
        $year      = $request->get('year');
        $date_from = $request->get('date_from');
        $date_to   = $request->get('date_to');
        $disposed  = $request->boolean('disposed', false); // show disposed leads?

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

        // Disposed = fixed / cancelled / recalled
        $disposedStatuses = Statuses::RET_DISPOSED_STATUSES;

        // NOT ISSUED base scope
        $niBase = Lead::whereNotNull('not_issued_at')
                      ->whereNull('not_issued_resolved_at')
                      ->whereNotNull('pending_contract_at');

        // NOT PAID base scope
        $npBase = Lead::whereNotNull('not_paid_at')
                      ->whereNull('paid_at')
                      ->whereNull('policy_died_at');

        // ----- KPI counts (all time, across both scopes) -----
        // A lead is "recalled" if recall_requested_at is set OR ret_action_status = 'recalled'.
        // A lead is "pending" only when it has no ret_action_status (or = 'pending') AND is NOT recalled.
        $kpi = [];
        foreach (array_keys(Statuses::RET_ACTION_STATUSES) as $s) {
            if ($s === 'recalled') {
                $kpi[$s] = $niBase->clone()->where(fn($q) => $q->whereNotNull('recall_requested_at')->orWhere('ret_action_status', 'recalled'))->count()
                         + $npBase->clone()->where(fn($q) => $q->whereNotNull('recall_requested_at')->orWhere('ret_action_status', 'recalled'))->count();
            } elseif ($s === 'pending') {
                $kpi[$s] = $niBase->clone()->where(fn($q) => $q->whereNull('ret_action_status')->orWhere('ret_action_status', 'pending'))->whereNull('recall_requested_at')->count()
                         + $npBase->clone()->where(fn($q) => $q->whereNull('ret_action_status')->orWhere('ret_action_status', 'pending'))->whereNull('recall_requested_at')->count();
            } else {
                $kpi[$s] = $niBase->clone()->where('ret_action_status', $s)->count()
                         + $npBase->clone()->where('ret_action_status', $s)->count();
            }
        }

        // ----- Active counts (filter for the counts KPI numbers above the tabs) -----
        $not_issued_count = $niBase->clone()->whereNotIn('ret_action_status', $disposedStatuses)->whereNotNull('pending_contract_at')->count()
                          + $niBase->clone()->whereNull('ret_action_status')->count();
        // simplify:
        $not_issued_count = $niBase->clone()
            ->where(fn($q) => $q->whereNull('ret_action_status')->orWhereNotIn('ret_action_status', $disposedStatuses))
            ->count();
        $not_paid_count   = $npBase->clone()
            ->where(fn($q) => $q->whereNull('ret_action_status')->orWhereNotIn('ret_action_status', $disposedStatuses))
            ->count();

        // ----- Table queries -----
        if ($disposed) {
            // Show disposed leads
            $ni_query = Lead::with(['insuranceCarrier', 'notIssuedBy', 'retActionUpdatedBy', 'recallRequestedBy'])
                ->whereNotNull('not_issued_at')
                ->whereNull('not_issued_resolved_at')
                ->whereNotNull('pending_contract_at')
                ->whereIn('ret_action_status', $disposedStatuses);
            $np_query = Lead::with(['insuranceCarrier', 'notPaidBy', 'retActionUpdatedBy', 'recallRequestedBy'])
                ->whereNotNull('not_paid_at')
                ->whereNull('paid_at')
                ->whereNull('policy_died_at')
                ->whereIn('ret_action_status', $disposedStatuses);
        } else {
            // Show active (non-disposed) leads
            $ni_query = Lead::with(['insuranceCarrier', 'notIssuedBy', 'retActionUpdatedBy', 'recallRequestedBy'])
                ->whereNotNull('not_issued_at')
                ->whereNull('not_issued_resolved_at')
                ->whereNotNull('pending_contract_at')
                ->where(fn($q) => $q->whereNull('ret_action_status')->orWhereNotIn('ret_action_status', $disposedStatuses));
            $np_query = Lead::with(['insuranceCarrier', 'notPaidBy', 'retActionUpdatedBy', 'recallRequestedBy'])
                ->whereNotNull('not_paid_at')
                ->whereNull('paid_at')
                ->whereNull('policy_died_at')
                ->where(fn($q) => $q->whereNull('ret_action_status')->orWhereNotIn('ret_action_status', $disposedStatuses));
        }

        $ni_query = $applyFilters($ni_query);
        $np_query = $applyFilters($np_query);

        $not_issued_leads = $ni_query->latest('not_issued_at')->paginate(50, ['*'], 'not_issued_page');
        $not_paid_leads   = $np_query->latest('not_paid_at')->paginate(50, ['*'], 'not_paid_page');

        $hasZoomToken = \App\Models\ZoomToken::where('user_id', Auth::id())
            ->where('expires_at', '>', now())
            ->exists();

        return view('admin.retention.index', compact(
            'not_issued_leads',
            'not_paid_leads',
            'not_issued_count',
            'not_paid_count',
            'kpi',
            'disposed',
            'hasZoomToken',
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
