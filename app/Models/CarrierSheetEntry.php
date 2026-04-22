<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarrierSheetEntry extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'carrier_sheet_rate_id',
        'sr_number',
        'entry_date',
        'policy_number',
        'name',
        'face_value',
        'premium',
        'policy_type',
        'status',
        'draft_date',
        'payment_date',
        'commission',
        'paid_amount',
        'balance',
        'chargeback_amount',
        'rate_override',
        'notes',
        'period_month',
        'created_by',
    ];

    protected $casts = [
        'entry_date'        => 'date',
        'draft_date'        => 'date',
        'payment_date'      => 'date',
        'period_month'      => 'date',
        'premium'           => 'decimal:2',
        'commission'        => 'decimal:2',
        'paid_amount'       => 'decimal:2',
        'balance'           => 'decimal:2',
        'chargeback_amount' => 'decimal:2',
        'rate_override'     => 'decimal:4',
        'sr_number'         => 'integer',
    ];

    /**
     * Cache the lead lookup to avoid multiple queries per entry.
     */
    protected $leadCache = null;
    protected $leadCacheLoaded = false;

    /* ── Relationships ─────────────────────────────────── */

    public function carrierRate(): BelongsTo
    {
        return $this->belongsTo(CarrierSheetRate::class, 'carrier_sheet_rate_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the associated lead by policy number (primary) or name (fallback).
     * Implements intelligent matching with caching to avoid repeated queries.
     * 
     * Matching priority:
     * 1. Exact policy_number match
     * 2. Exact cn_name match
     * 3. Case-insensitive partial name match
     */
    public function lead()
    {
        // Return cached result if already loaded
        if ($this->leadCacheLoaded) {
            return $this->leadCache;
        }

        $lead = null;

        // Try matching by policy number first (most reliable)
        if (!empty($this->policy_number)) {
            $lead = Lead::where('policy_number', $this->policy_number)->first();
            if ($lead) {
                $this->leadCache = $lead;
                $this->leadCacheLoaded = true;
                return $lead;
            }
        }

        // Fallback: match by name if policy number didn't work
        if (!empty($this->name)) {
            // Try exact match first
            $lead = Lead::where('cn_name', $this->name)->first();
            if ($lead) {
                $this->leadCache = $lead;
                $this->leadCacheLoaded = true;
                return $lead;
            }

            // Try case-insensitive partial match as last resort
            $lead = Lead::whereRaw('LOWER(cn_name) LIKE ?', ['%' . strtolower($this->name) . '%'])
                ->orderByRaw('LENGTH(cn_name) ASC') // Prefer shorter matches (more precise)
                ->first();
        }

        // Cache the result (even if null) to avoid repeated queries
        $this->leadCache = $lead;
        $this->leadCacheLoaded = true;

        return $lead;
    }

    /* ── Status constants ──────────────────────────────── */

    public const STATUS_APPROVED   = 'approved';
    public const STATUS_PAID       = 'paid';
    public const STATUS_CHARGEBACK = 'chargeback';
    public const STATUS_DECLINED   = 'declined';

    public const STATUSES = [
        self::STATUS_APPROVED,
        self::STATUS_PAID,
        self::STATUS_CHARGEBACK,
        self::STATUS_DECLINED,
    ];

    /* ── Scopes ────────────────────────────────────────── */

    public function scopeForPeriod($query, ?string $month)
    {
        if ($month) {
            return $query->where('period_month', $month);
        }
        return $query;
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /* ── Helpers ───────────────────────────────────────── */

    public function isDeclined(): bool
    {
        return strtolower($this->status) === self::STATUS_DECLINED;
    }

    public function isChargeback(): bool
    {
        return strtolower($this->status) === self::STATUS_CHARGEBACK;
    }

    public function isPaid(): bool
    {
        return strtolower($this->status) === self::STATUS_PAID;
    }

    /**
     * Get the status badge color class.
     */
    public function getStatusColor(): string
    {
        return match (strtolower($this->status)) {
            'approved'   => '#FFF8E1',
            'paid'       => '#E8F5E9',
            'chargeback' => '#FFEBEE',
            'declined'   => '#FFF3E0',
            default      => '#FFFFFF',
        };
    }

    public function getStatusBadgeClass(): string
    {
        return match (strtolower($this->status)) {
            'approved'   => 'bg-warning text-dark',
            'paid'       => 'bg-success',
            'chargeback' => 'bg-danger',
            'declined'   => 'bg-orange',
            default      => 'bg-secondary',
        };
    }

    /**
     * Get sales pipeline stage abbreviation and info.
     * Returns ['label' => 'PC', 'name' => 'Pending Contract', 'color' => '#color']
     */
    public function getPipelineStage(): array
    {
        $lead = $this->lead();

        if (!$lead) {
            return [
                'label' => '—',
                'name'  => 'Unknown',
                'color' => $this->getPipelineColor(),
            ];
        }

        // Determine pipeline stage based on lead timestamps
        if ($lead->paid_sale_at) {
            $label = 'PS';
            $name = 'Paid Sales';
        } elseif ($lead->pending_draft_at) {
            $label = 'PD';
            $name = 'Pending Draft';
        } elseif ($lead->assigned_followup_person) {
            $label = 'FU';
            $name = 'Followup';
        } elseif ($lead->pending_contract_at) {
            $label = 'PC';
            $name = 'Pending Contract';
        } elseif ($lead->pending_approval_at) {
            $label = 'PA';
            $name = 'Pending Approval';
        } else {
            $label = 'SR';
            $name = 'Sales Record';
        }

        return [
            'label' => $label,
            'name'  => $name,
            'color' => $this->getPipelineColor(),
        ];
    }

    /**
     * Get color for pipeline badge based on current entry status.
     */
    public function getPipelineColor(): string
    {
        return match (strtolower($this->status)) {
            'approved'   => '#FFC107', // Yellow
            'paid'       => '#28A745', // Green
            'chargeback' => '#DC3545', // Red
            'declined'   => '#DC3545', // Red
            default      => '#6C757D', // Gray
        };
    }
}
