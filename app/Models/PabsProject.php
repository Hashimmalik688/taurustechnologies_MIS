<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PabsProject extends Model
{
    use HasFactory;

    protected $table = 'pabs_projects';

    protected $fillable = [
        'project_code',
        'project_name',
        'description',
        'section_id',
        'status',
        'scoping_document_path',
        'scoping_completed_at',
        'vendor_a_quote',
        'vendor_a_name',
        'vendor_b_quote',
        'vendor_b_name',
        'vendor_c_quote',
        'vendor_c_name',
        'approval_status',
        'priority',
        'approved_budget',
        'target_deadline',
        'approval_notes',
        'actual_cost',
        'total_budget',
        'started_at',
        'completed_at',
        'variance_flagged',
        'variance_notes',
        'created_by',
        'scoping_lead_id',
        'approved_by',
        'allocated_by',
        'assigned_to',
        'approved_at',
        'allocated_at',
    ];

    protected $casts = [
        'scoping_completed_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'approved_at' => 'datetime',
        'allocated_at' => 'datetime',
        'target_deadline' => 'date',
        'vendor_a_quote' => 'decimal:2',
        'vendor_b_quote' => 'decimal:2',
        'vendor_c_quote' => 'decimal:2',
        'approved_budget' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'total_budget' => 'decimal:2',
        'variance_flagged' => 'boolean',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopingLead()
    {
        return $this->belongsTo(User::class, 'scoping_lead_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function allocatedBy()
    {
        return $this->belongsTo(User::class, 'allocated_by');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function approvals()
    {
        return $this->hasMany(PabsProjectApproval::class, 'project_id');
    }

    public function comments()
    {
        return $this->hasMany(PabsProjectComment::class, 'project_id');
    }

    public function tickets()
    {
        return $this->hasMany(PabsTicket::class, 'project_id');
    }

    // Scopes
    public function scopeBySection($query, $sectionId)
    {
        return $query->where('section_id', $sectionId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'PENDING APPROVAL');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['SCOPING', 'QUOTING', 'PENDING APPROVAL', 'BUDGET ALLOCATED', 'IN PROGRESS']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'COMPLETED');
    }

    // Helpers
    public function getLowestQuote()
    {
        $quotes = [
            $this->vendor_a_quote,
            $this->vendor_b_quote,
            $this->vendor_c_quote,
        ];
        
        return collect($quotes)->filter()->min();
    }

    public function getAverageQuote()
    {
        $quotes = [
            $this->vendor_a_quote,
            $this->vendor_b_quote,
            $this->vendor_c_quote,
        ];
        
        return collect($quotes)->filter()->avg();
    }

    public function hasVariance()
    {
        if ($this->actual_cost && $this->approved_budget) {
            return $this->actual_cost > $this->approved_budget;
        }
        return false;
    }

    public function getVarianceAmount()
    {
        if ($this->actual_cost && $this->approved_budget) {
            return $this->actual_cost - $this->approved_budget;
        }
        return 0;
    }

    public function getVariancePercentage()
    {
        if ($this->actual_cost && $this->approved_budget && $this->approved_budget > 0) {
            return (($this->actual_cost - $this->approved_budget) / $this->approved_budget) * 100;
        }
        return 0;
    }
}
