<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AllowedDevice extends Model
{
    protected $fillable = [
        'device_token',
        'status',
        'label',
        'name',
        'added_by',
        'last_seen_at',
        'last_seen_ip',
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
    ];

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function isApproved(): bool  { return $this->status === 'approved'; }
    public function isPending(): bool   { return $this->status === 'pending'; }
    public function isDisabled(): bool  { return $this->status === 'disabled'; }
    public function isRejected(): bool  { return $this->status === 'rejected'; }
}
