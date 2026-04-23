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
     * 1. Exact policy_number match (skips placeholder values like NA, N/A, TBD)
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
        // Skip placeholder values like NA, N.A, N/A, TBD, etc.
        if (!empty($this->policy_number) && !$this->isPlaceholderPolicyNumber($this->policy_number)) {
            $lead = Lead::where('policy_number', $this->policy_number)->first();
            if ($lead) {
                $this->leadCache = $lead;
                $this->leadCacheLoaded = true;
                return $lead;
            }
        }

        // Fallback: match by name if policy number didn't work
        if (!empty($this->name)) {
            // Get carrier name for this entry to help with matching
            $carrierName = $this->carrierRate?->carrier_label;
            
            // Try exact match first
            $query = Lead::where('cn_name', $this->name);
            
            // If carrier is known, prefer matching by carrier too (handles duplicate names)
            if ($carrierName) {
                // Try to extract carrier name from label (e.g., "MOO (J-1)" -> "Mutual of Omaha")
                $leadWithCarrier = (clone $query)->whereHas('insuranceCarrier', function($q) use ($carrierName) {
                    $q->where('name', 'LIKE', '%' . $this->extractCarrierKeyword($carrierName) . '%');
                })->first();
                
                // Also check carrier_name field directly (legacy compatibility)
                if (!$leadWithCarrier) {
                    $leadWithCarrier = (clone $query)->where('carrier_name', 'LIKE', '%' . $this->extractCarrierKeyword($carrierName) . '%')->first();
                }
                
                if ($leadWithCarrier) {
                    $lead = $leadWithCarrier;
                }
            }
            
            // If no carrier-specific match, use any exact name match
            if (!$lead) {
                $lead = $query->first();
            }
            
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

    /**
     * Check if a policy number is a placeholder value (not a real policy number).
     * Placeholder values should be skipped during lead matching to avoid false positives.
     */
    private function isPlaceholderPolicyNumber(?string $policyNumber): bool
    {
        if (empty($policyNumber)) {
            return true;
        }

        $normalized = strtoupper(trim($policyNumber));
        
        // Common placeholder patterns
        $placeholders = [
            'NA',
            'N.A',
            'N.A.',
            'N/A',
            'TBD',
            'PENDING',
            'NONE',
            'NULL',
            '--',
            '---',
            'N\\A', // Escaped slash
        ];

        return in_array($normalized, $placeholders);
    }

    /**
     * Extract carrier keyword from carrier label for matching.
     * E.g., "MOO (J-1)" -> "Mutual of Omaha", "AIG E-1" -> "AIG"
     */
    private function extractCarrierKeyword(?string $carrierLabel): string
    {
        if (empty($carrierLabel)) {
            return '';
        }

        // Remove partner codes and extra info in parentheses
        $keyword = preg_replace('/\s*\([^)]*\)/', '', $carrierLabel);
        $keyword = trim($keyword);
        
        // Get first word/acronym
        $parts = explode(' ', $keyword);
        $acronym = $parts[0] ?? '';
        
        // Map common carrier acronyms to full names for better matching
        $acronymMap = [
            'MOO' => 'Mutual of Omaha',
            'MoO' => 'Mutual of Omaha',
            'AIG' => 'AIG',
            'TA' => 'Transamerica',
            'SEC' => 'Securian',
            'AMAM' => 'AMAM',
            'RA' => 'Royal Arcanum',
            'R.A' => 'Royal Arcanum',
            'AETNA' => 'Aetna',
            'GTL' => 'GTL',
        ];
        
        return $acronymMap[strtoupper($acronym)] ?? $acronym;
    }

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
                'color' => '#6C757D', // Gray
            ];
        }

        // Chargeback status takes precedence
        if ($this->isChargeback()) {
            return [
                'label' => 'CB',
                'name'  => 'Chargeback',
                'color' => '#DC3545', // Red
            ];
        }

        // Paid Sales
        if ($lead->paid_sale_at) {
            return [
                'label' => 'PAID',
                'name'  => 'Paid Sales',
                'color' => '#28A745', // Green
            ];
        }

        // Pending Draft
        if ($lead->pending_draft_at) {
            $color = $lead->not_paid_fdfp_type ? '#DC3545' : '#800020'; // Red (Not Paid) : Maroon (Pending)
            return [
                'label' => 'PD',
                'name'  => 'Pending Draft',
                'color' => $color,
            ];
        }

        // Pending Contract
        if ($lead->pending_contract_at) {
            // Determine color based on status
            if ($lead->not_issued_at && !$lead->not_issued_resolved_at) {
                $color = '#FF69B4'; // Pink (Not Issued)
            } elseif ($lead->assigned_followup_person) {
                $color = '#FF8C00'; // Orange (Followup)
            } elseif ($lead->issuance_status === 'Issued') {
                $color = '#800080'; // Purple (Issued)
            } else {
                $color = '#FFC107'; // Yellow (Pending)
            }
            return [
                'label' => 'PC',
                'name'  => 'Pending Contract',
                'color' => $color,
            ];
        }

        // Pending Submission (Pending Approval)
        if ($lead->pending_approval_at) {
            $color = $this->isDeclined() ? '#DC3545' : '#007BFF'; // Red (Declined) : Blue (Pending)
            return [
                'label' => 'PS',
                'name'  => 'Pending Submission',
                'color' => $color,
            ];
        }

        // Sales Record (default)
        return [
            'label' => 'SR',
            'name'  => 'Sales Record',
            'color' => '#6C757D', // Gray
        ];
    }
}
