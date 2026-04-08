<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Tracks which lead fields were last updated (for cross-page highlight badges).
 *
 * One row per (lead_id, field_name) — upserted on every field change.
 * Provides a persistent "Updated by [user] at [time]" badge visible anywhere
 * the lead is displayed.
 */
class LeadFieldHighlight extends Model
{
    /** No created_at — only updated_at (maintained manually) */
    public $timestamps = false;

    protected $fillable = [
        'lead_id',
        'field_name',
        'updated_by_id',
        'updated_at',
    ];

    protected $casts = [
        'updated_at' => 'datetime',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }
}
