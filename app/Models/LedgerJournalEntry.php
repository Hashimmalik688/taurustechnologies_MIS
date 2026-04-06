<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LedgerJournalEntry extends Model
{
    protected $fillable = [
        'entry_number',
        'entry_date',
        'type',
        'reference',
        'description',
        'insured_name',
        'is_posted',
        'total_debit',
        'gross_amount',
        'our_share_percentage',
        'created_by',
        'lead_id',
    ];

    protected $casts = [
        'entry_date'           => 'date',
        'is_posted'            => 'boolean',
        'total_debit'          => 'decimal:2',
        'gross_amount'         => 'decimal:2',
        'our_share_percentage' => 'decimal:4',
    ];

    // ─── Relations ────────────────────────────────────────────────────────────

    public function lines(): HasMany
    {
        return $this->hasMany(LedgerJournalEntryLine::class, 'journal_entry_id')
                    ->orderBy('sort_order');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** The paid lead this journal entry was created from */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Lead::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Generate the next sequential entry number in the format JE-YYYY-NNNN.
     */
    public static function generateEntryNumber(): string
    {
        $year   = now()->year;
        $prefix = "JE-{$year}-";

        $last = self::where('entry_number', 'like', $prefix . '%')
                    ->orderByDesc('entry_number')
                    ->value('entry_number');

        $seq = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeDateRange($query, ?string $from, ?string $to)
    {
        if ($from) {
            $query->where('entry_date', '>=', $from);
        }
        if ($to) {
            $query->where('entry_date', '<=', $to);
        }
        return $query;
    }

    // ─── Type Labels ──────────────────────────────────────────────────────────

    public static function typeLabel(string $type): string
    {
        return match($type) {
            'sale'             => 'Sale',
            'payment_received' => 'Payment Received',
            'opening_balance'  => 'Opening Balance',
            'general'          => 'General Journal',
            default            => ucfirst($type),
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return self::typeLabel($this->type);
    }
}
