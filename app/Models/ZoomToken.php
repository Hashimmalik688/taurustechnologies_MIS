<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ZoomToken extends Model
{
    use SoftDeletes;

    protected $table = 'zoom_tokens';
    
    protected $fillable = [
        'account_id',
        'access_token',
        'refresh_token',
        'expires_at',
        'token_type',
        'scopes',
        'auth_type',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'scopes' => 'array',
    ];

    /**
     * Check if access token is expired.
     */
    public function isExpired(): bool
    {
        if (!$this->expires_at) {
            return false;
        }
        return now()->isAfter($this->expires_at);
    }

    /**
     * Scope for active (non-expired) tokens.
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }
}
