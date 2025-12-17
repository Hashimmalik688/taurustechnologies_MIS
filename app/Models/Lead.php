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
        'ssn',
        'address',
        'carrier_name',
        'insurance_carrier_id',
        'coverage_amount',
        'monthly_premium',
        'beneficiary',
        'beneficiary_dob',
        'emergency_contact',
        'policy_type',
        'initial_draft_date',
        'future_draft_date',
        'bank_name',
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
        'managed_by',
        'verified_by',
        'validated_by',
        'assigned_validator_id',
        'age',
        'state',
        'zip_code',
        'preset_line',
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
        'initial_draft_date' => 'date',
        'future_draft_date' => 'date',
        'ss_date' => 'date',
        'chargeback_marked_date' => 'datetime',
        'retained_at' => 'datetime',
        'smoker' => 'boolean',
        'is_rewrite' => 'boolean',
        'driving_license' => 'boolean',
        'card_number' => 'encrypted',
        'cvv' => 'encrypted',
        'ss_amount' => 'decimal:2',
        'bank_balance' => 'decimal:2',
        'coverage_amount' => 'decimal:2',
        'monthly_premium' => 'decimal:2',
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
}
