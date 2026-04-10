<?php

namespace App\Models;

use App\Support\Statuses;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'date',
        'phone_number',
        'secondary_phone_number',
        'cn_name',
        'date_of_birth',
        'gender',
        'smoker',
        'driving_license',
        'driving_license_number',
        'height',
        'weight',
        'height_weight',
        'birth_place',
        'medical_issue',
        'medications',
        'doctor_name',
        'doctor_number',
        'doctor_address',
        'ssn',
        'address',
        'carrier_name',
        'insurance_carrier_id',
        'coverage_amount',
        'monthly_premium',
        'beneficiary',
        'beneficiary_dob',
        'beneficiaries',  // JSON field for multiple beneficiaries
        'emergency_contact',
        'policy_type',
        'policy_number',
        'initial_draft_date',
        'future_draft_date',
        'bank_name',
        'account_title',
        'account_type',
        'routing_number',
        'account_number',
        'acc_number',
        'card_info',
        'account_verified_by',
        'bank_balance',
        'ss_amount',
        'ss_date',
        'card_number',
        'cvv',
        'expiry_date',
        'source',
        'source_type',
        'team',
        'closer_name',
        'closer_id',
        'assigned_partner',
        'managed_by',
        'verified_by',
        'validated_by',
        'assigned_validator_id',
        'age',
        'state',
        'zip_code',
        'preset_line',
        'settlement_type',
        'comments',

        // Retention fields
        'retention_status',
        'retained_at',
        'chargeback_marked_date',
        'chargeback_marked_by_id',
        'chargeback_paid_at',
        'chargeback_paid_by_id',
        'cb_sent_to_retention_at',
        'cb_sent_to_retention_by_id',
        'is_rewrite',
        'rewrite_source_lead_id',
        'rewrite_sent_back_at',
        'retention_notes',
        'retention_officer_id',
        'ret_action_status',
        'ret_action_updated_at',
        'ret_action_updated_by',
        'retention_disposition',

        // QA fields
        'qa_status',
        'qa_reason',
        'qa_user_id',
        'qa_reviewed_at',

        // Submission review fields
        'submission_status',
        'submission_reason',
        'submission_by',
        'submission_at',

        // Ravens Validation fields
        'ravens_validated_at',
        'ravens_validated_by',
        'ravens_validation_status',

        // Application ID (assigned at Submissions stage)
        'app_id',

        // Assign Back / Recall fields
        'recall_requested_at',
        'recall_requested_by',
        'recall_note',

        // Sale tracking
        'sale_date',
        'sale_at',
        'resale_count',
        'resale_log',

        // Issuance fields
        'issuance_status',
        'issuance_date',
        'issuance_reason',
        'issued_by',
        'issued_policy_number',
        'assigned_agent_id',
        'policy_number_set_at',
        'assigned_agent_set_at',
        'partner_id',
        'partner_set_at',
        'commission_paid_to_partner',
        'commission_paid_at',
        
        // Followup fields
        'assigned_followup_person',
        'followup_assigned_by',
        'followup_assigned_at',
        'followup_status',
        'followup_required',
        'followup_scheduled_at',
        
        // Bank Verification assignment fields
        'assigned_bank_verifier',
        'bank_verifier_assigned_by',
        'bank_verifier_assigned_at',
        'bank_verification_comment',
        
        // Revenue and commission tracking
        'agent_commission',
        'agent_revenue',
        'settlement_percentage',
        'commission_calculation_notes',
        'commission_calculated_at',

        // Issuance disposition fields (for incomplete issuances sent to retention)
        'issuance_disposition',
        'issuance_disposition_date',
        'disposition_officer_id',
        'has_other_insurances',

        // Disposition tracking
        'disposed_at',
        'disposed_by',
        'disposition_reason',

        // Callback notes (resets every 3 days)
        'callback_note',
        'callback_note_updated_at',

        // Bank Verification fields
        'bank_verification_status',
        'bank_verification_date',
        'bank_verification_notes',
        'bank_verified_by',

        // Stage-specific timestamps
        'verified_at',
        'transferred_at',
        'closed_at',
        'validated_at',
        'returned_at',
        'declined_at',

        // ── Sales Pipeline Stage fields ────────────────────────────────────
        // Pendings Approved → Pending Contract transition
        'pending_contract_at',
        'pending_contract_by_id',

        // Not Issued (Retention exception at Pendings Approved)
        'not_issued_disposition',
        'not_issued_comment',
        'not_issued_at',
        'not_issued_by_id',
        'not_issued_resolved_by_id',
        'not_issued_resolved_at',

        // Followup Done
        'followup_done_at',
        'followup_done_by_id',

        // Pending Draft
        'pending_draft_at',
        'pending_draft_by_id',

        // Not Paid / FDFP (Retention exception at Pending Draft)
        'not_paid_fdfp_type',
        'not_paid_manual_disposition',
        'not_paid_at',
        'not_paid_by_id',
        'not_paid_comment',

        // Paid Sales
        'paid_at',
        'paid_by_id',

        // Accounting Ledger links
        'ledger_journal_entry_id',
        'ledger_sales_return_entry_id',
        'ledger_chargeback_paid_entry_id',

        // Policy Died
        'policy_died_reason',
        'policy_died_at',
        'policy_died_by_id',

        'status',
        'decline_reason',
        'pending_reason',
        'staff_notes',
        'closer_qna',
        'manager_notes',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // Encrypted attributes
    protected $hidden = [
        'card_number',
        'cvv',
        'ssn',
        'card_info',
    ];

    // Cast attributes
    protected $casts = [
        'sale_at' => 'datetime',
        'sale_date' => 'date',
        'date_of_birth' => 'date',
        'beneficiary_dob' => 'date',
        'beneficiaries' => 'array',  // Cast JSON to array
        'initial_draft_date' => 'date',
        'future_draft_date' => 'date',
        'ss_date' => 'date',
        'chargeback_marked_date' => 'datetime',
        'chargeback_paid_at'     => 'datetime',
        'retained_at' => 'datetime',
        'smoker' => 'boolean',
        'is_rewrite' => 'boolean',
        'driving_license' => 'boolean',
        'followup_required' => 'boolean',
        'followup_scheduled_at' => 'datetime',
        'closer_qna' => 'array',
        'card_number' => 'encrypted',
        'cvv' => 'encrypted',
        'ss_amount' => 'decimal:2',
        'bank_balance' => 'decimal:2',
        'coverage_amount' => 'decimal:2',
        'monthly_premium' => 'decimal:2',
        'agent_commission' => 'decimal:2',
        'agent_revenue' => 'decimal:2',
        'settlement_percentage' => 'decimal:2',
        'commission_calculated_at' => 'datetime',
        'verified_at' => 'datetime',
        'transferred_at' => 'datetime',
        'closed_at' => 'datetime',
        'validated_at' => 'datetime',
        'returned_at' => 'datetime',
        'declined_at' => 'datetime',
        'callback_note_updated_at' => 'datetime',
        'qa_reviewed_at' => 'datetime',
        'submission_at' => 'datetime',
        'ravens_validated_at' => 'datetime',
        'recall_requested_at' => 'datetime',
        'followup_assigned_at' => 'datetime',
        'bank_verifier_assigned_at' => 'datetime',
        'ret_action_updated_at' => 'datetime',
        'resale_log' => 'array',

        // Sales pipeline stage timestamps
        'pending_contract_at'      => 'datetime',
        'not_issued_at'            => 'datetime',
        'not_issued_resolved_at'   => 'datetime',
        'followup_done_at'         => 'datetime',
        'pending_draft_at'         => 'datetime',
        'not_paid_at'              => 'datetime',
        'paid_at'                  => 'datetime',
        'policy_died_at'           => 'datetime',
        'chargeback_paid_at'          => 'datetime',
        'cb_sent_to_retention_at'    => 'datetime',
    ];

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically set chargeback_marked_date when status changes to chargeback
        static::updating(function ($lead) {
            if ($lead->isDirty('status') && $lead->status === Statuses::LEAD_CHARGEBACK) {
                if (empty($lead->chargeback_marked_date)) {
                    $lead->chargeback_marked_date = now();
                }
            }
        });

        // Auto-assign QA status when lead comes to sales (status = accepted)
        static::creating(function ($lead) {
            if ($lead->status === Statuses::LEAD_ACCEPTED) {
                $lead->qa_status = Statuses::QA_IN_REVIEW;
            }
        });

        static::updating(function ($lead) {
            if ($lead->isDirty('status') && $lead->status === Statuses::LEAD_ACCEPTED && empty($lead->qa_status)) {
                $lead->qa_status = Statuses::QA_IN_REVIEW;
            }
        });
    }

    /**
     * Get age: return stored value or calculate from date_of_birth.
     */
    public function getAgeAttribute($value): ?int
    {
        if (!empty($value)) {
            return (int) $value;
        }

        if (!empty($this->attributes['date_of_birth'])) {
            return \Carbon\Carbon::parse($this->attributes['date_of_birth'])->age;
        }

        return null;
    }

    public function carriers()
    {
        return $this->hasMany(Carrier::class);
    }

    /**
     * Get call logs for this lead
     */
    public function callLogs()
    {
        return $this->hasMany(CallLog::class)->latest();
    }

    /**
     * Get dial records for this lead (tracks which closers dialed it)
     */
    public function dials()
    {
        return $this->hasMany(LeadDial::class);
    }

    /**
     * Get the user who forwarded this lead
     */
    public function forwardedBy()
    {
        return $this->belongsTo(User::class, 'forwarded_by');
    }

    /**
     * Get the user who manages this lead
     */
    public function managedBy()
    {
        return $this->belongsTo(User::class, 'managed_by');
    }

    /**
     * Get the user who validated this lead
     */
    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    /**
     * Get the user assigned as validator for this lead
     */
    public function assignedValidator()
    {
        return $this->belongsTo(User::class, 'assigned_validator_id');
    }

    /**
     * Get the closer assigned to this lead
     */
    public function assignedCloser()
    {
        return $this->belongsTo(User::class, 'managed_by');
    }

    /**
     * Get the verifier who verified this lead
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get ledger entries for this lead
     */
    public function ledgerEntries()
    {
        return $this->hasMany(LedgerEntry::class);
    }

    /**
     * Get the insurance carrier for this lead
     */
    public function insuranceCarrier()
    {
        return $this->belongsTo(InsuranceCarrier::class, 'insurance_carrier_id');
    }

    /**
     * Get the retention officer assigned to this lead
     */
    public function retentionOfficer()
    {
        return $this->belongsTo(User::class, 'retention_officer_id');
    }

    /**
     * Get the disposition officer who marked issuance disposition
     */
    public function dispositionOfficer()
    {
        return $this->belongsTo(User::class, 'disposition_officer_id');
    }

    /**
     * Get the QA user assigned to this lead
     */
    public function qaUser()
    {
        return $this->belongsTo(User::class, 'qa_user_id');
    }

    /**
     * Get the submission reviewer assigned to this lead
     */
    public function submissionReviewer()
    {
        return $this->belongsTo(User::class, 'submission_by');
    }

    /**
     * Get the user who issued this lead
     */
    public function issuedByUser()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    /**
     * Get the agent assigned to this issued lead
     */
    public function assignedAgent()
    {
        return $this->belongsTo(User::class, 'assigned_agent_id');
    }

    /**
     * Get the partner assigned to this issued lead
     */
    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    /**
     * Get the user assigned for followup
     */
    public function followupPerson()
    {
        return $this->belongsTo(User::class, 'assigned_followup_person');
    }

    /**
     * Get the user who assigned the followup person
     */
    public function followupAssignedByUser()
    {
        return $this->belongsTo(User::class, 'followup_assigned_by');
    }

    /**
     * Get the user assigned for bank verification
     */
    public function bankVerifier()
    {
        return $this->belongsTo(User::class, 'assigned_bank_verifier');
    }

    /**
     * Get the user who assigned the bank verifier
     */
    public function bankVerifierAssignedByUser()
    {
        return $this->belongsTo(User::class, 'bank_verifier_assigned_by');
    }

    /**
     * Get the user who performed the bank verification
     */
    public function bankVerifiedByUser()
    {
        return $this->belongsTo(User::class, 'bank_verified_by');
    }

    // ── Sales Pipeline Stage relationships ────────────────────────────────────

    /** Manager who sent this lead from Pendings Approved to Pending Contract */
    public function pendingContractBy()
    {
        return $this->belongsTo(User::class, 'pending_contract_by_id');
    }

    /** Manager who marked this lead as Not Issued */
    public function notIssuedBy()
    {
        return $this->belongsTo(User::class, 'not_issued_by_id');
    }

    /** Retention officer who resolved the Not Issued block */
    public function notIssuedResolvedBy()
    {
        return $this->belongsTo(User::class, 'not_issued_resolved_by_id');
    }

    /** Closer who marked followup as done */
    public function followupDoneBy()
    {
        return $this->belongsTo(User::class, 'followup_done_by_id');
    }

    /** Manager who moved lead to Pending Draft */
    public function pendingDraftBy()
    {
        return $this->belongsTo(User::class, 'pending_draft_by_id');
    }

    /** Retention officer who marked lead as Not Paid (FDFP) */
    public function notPaidBy()
    {
        return $this->belongsTo(User::class, 'not_paid_by_id');
    }

    /** User who last updated retention action status */
    public function retActionUpdatedBy()
    {
        return $this->belongsTo(User::class, 'ret_action_updated_by');
    }

    /** User who recalled this lead to the closer */
    public function recallRequestedBy()
    {
        return $this->belongsTo(User::class, 'recall_requested_by');
    }

    /** Field-level highlight records (cross-page updated indicator) */
    public function fieldHighlights()
    {
        return $this->hasMany(LeadFieldHighlight::class)->with('updatedBy');
    }

    /** Manager/Finance who marked lead as Paid */
    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by_id');
    }

    /** User who marked this lead as chargeback */
    public function chargebackMarkedBy()
    {
        return $this->belongsTo(User::class, 'chargeback_marked_by_id');
    }

    /** User who marked the chargeback as recovered/paid */
    public function chargebackPaidBy()
    {
        return $this->belongsTo(User::class, 'chargeback_paid_by_id');
    }

    /** User who sent this chargeback to the Retention team */
    public function cbSentToRetentionBy()
    {
        return $this->belongsTo(User::class, 'cb_sent_to_retention_by_id');
    }

    /** The accounting journal entry created when this paid sale was posted to the ledger */
    public function ledgerJournalEntry()
    {
        return $this->belongsTo(\App\Models\LedgerJournalEntry::class, 'ledger_journal_entry_id');
    }

    /** The sales-return journal entry posted when this lead is chargebacked */
    public function ledgerSalesReturnEntry()
    {
        return $this->belongsTo(\App\Models\LedgerJournalEntry::class, 'ledger_sales_return_entry_id');
    }

    /** The chargeback recovery journal entry posted when a chargebacked lead is marked paid */
    public function ledgerChargebackPaidEntry()
    {
        return $this->belongsTo(\App\Models\LedgerJournalEntry::class, 'ledger_chargeback_paid_entry_id');
    }

    /** User who marked lead as Policy Died */
    public function policyDiedBy()
    {
        return $this->belongsTo(User::class, 'policy_died_by_id');
    }

    // ── Pipeline stage helper scopes ──────────────────────────────────────────

    /** Leads currently in Pendings Approved (validated, not yet sent to contract) */
    public function scopePendingsApproved($query)
    {
        return $query->where('ravens_validation_status', 'valid')
                     ->whereNull('pending_contract_at')
                     ->whereNull('not_issued_at');
    }

    /** Leads currently blocked as Not Issued (awaiting Retention to resolve) */
    public function scopeNotIssued($query)
    {
        return $query->whereNotNull('not_issued_at')
                     ->whereNull('not_issued_resolved_at')
                     ->whereNull('pending_contract_at');
    }

    /** Leads in Pending Contract stage */
    public function scopePendingContract($query)
    {
        return $query->whereNotNull('pending_contract_at')
                     ->where(function ($q) {
                         $q->whereNull('issuance_status')
                           ->orWhere('issuance_status', 'Pending');
                     })
                     ->whereNull('policy_died_at');
    }

    /** Leads in Followup (issued but closer hasn't marked followup done yet) */
    public function scopeFollowupPending($query)
    {
        return $query->where('issuance_status', 'Issued')
                     ->whereNull('followup_done_at')
                     ->whereNull('policy_died_at');
    }

    /** Leads in Pending Draft (followup done, awaiting first premium draft) */
    public function scopePendingDraft($query)
    {
        return $query->whereNotNull('followup_done_at')
                     ->whereNull('paid_at')
                     ->whereNull('policy_died_at');
    }

    /** Leads that are Paid Sales */
    public function scopePaidSales($query)
    {
        return $query->whereNotNull('paid_at');
    }

    /** Leads that have Died (no retention action; re-dialable) */
    public function scopePolicyDied($query)
    {
        return $query->whereNotNull('policy_died_at');
    }
}
