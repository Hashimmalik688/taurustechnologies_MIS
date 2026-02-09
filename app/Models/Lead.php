<?php

namespace App\Models;

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
        'team',
        'closer_name',
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
        'is_rewrite',
        'retention_notes',
        'retention_officer_id',

        // QA fields
        'qa_status',
        'qa_reason',
        'qa_user_id',

        // Manager fields
        'manager_status',
        'manager_reason',
        'manager_user_id',

        // Manager fields
        'manager_status',
        'manager_reason',
        'manager_user_id',

        // Sale tracking
        'sale_date',
        'sale_at',

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
        
        // Followup fields
        'assigned_followup_person',
        'followup_status',
        'followup_required',
        'followup_scheduled_at',
        
        // Bank Verification assignment fields
        'assigned_bank_verifier',
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

        // Bank Verification fields
        'bank_verification_status',
        'bank_verification_date',
        'bank_verification_notes',

        // Stage-specific timestamps
        'verified_at',
        'transferred_at',
        'closed_at',
        'validated_at',
        'returned_at',
        'declined_at',

        'status',
        'decline_reason',
        'pending_reason',
        'staff_notes',
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
        'sale_at' => 'date',
        'sale_date' => 'date',
        'date_of_birth' => 'date',
        'beneficiary_dob' => 'date',
        'beneficiaries' => 'array',  // Cast JSON to array
        'initial_draft_date' => 'date',
        'future_draft_date' => 'date',
        'ss_date' => 'date',
        'chargeback_marked_date' => 'datetime',
        'retained_at' => 'datetime',
        'smoker' => 'boolean',
        'is_rewrite' => 'boolean',
        'driving_license' => 'boolean',
        'followup_required' => 'boolean',
        'followup_scheduled_at' => 'datetime',
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
    ];

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically set chargeback_marked_date when status changes to chargeback
        static::updating(function ($lead) {
            if ($lead->isDirty('status') && $lead->status === 'chargeback') {
                if (empty($lead->chargeback_marked_date)) {
                    $lead->chargeback_marked_date = now();
                }
            }
        });

        // Auto-assign QA status when lead comes to sales (status = accepted)
        static::creating(function ($lead) {
            if ($lead->status === 'accepted') {
                $lead->qa_status = 'In Review';
            }
        });

        static::updating(function ($lead) {
            if ($lead->isDirty('status') && $lead->status === 'accepted' && empty($lead->qa_status)) {
                $lead->qa_status = 'In Review';
            }
        });
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
     * Get the manager user assigned to this lead
     */
    public function managerUser()
    {
        return $this->belongsTo(User::class, 'manager_user_id');
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
     * Get the user assigned for bank verification
     */
    public function bankVerifier()
    {
        return $this->belongsTo(User::class, 'assigned_bank_verifier');
    }
}
