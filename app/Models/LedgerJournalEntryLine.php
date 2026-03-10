<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LedgerJournalEntryLine extends Model
{
    protected $fillable = [
        'journal_entry_id',
        'account_id',
        'partner_id',
        'insurance_carrier_id',
        'debit',
        'credit',
        'description',
        'sort_order',
    ];

    protected $casts = [
        'debit'  => 'decimal:2',
        'credit' => 'decimal:2',
    ];

    // ─── Relations ────────────────────────────────────────────────────────────

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(LedgerJournalEntry::class, 'journal_entry_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    public function carrier(): BelongsTo
    {
        return $this->belongsTo(\App\Models\InsuranceCarrier::class, 'insurance_carrier_id');
    }
}
