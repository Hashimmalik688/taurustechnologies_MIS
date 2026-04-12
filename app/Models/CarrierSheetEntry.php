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

    /* ── Relationships ─────────────────────────────────── */

    public function carrierRate(): BelongsTo
    {
        return $this->belongsTo(CarrierSheetRate::class, 'carrier_sheet_rate_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
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
}
