<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ZoomToken extends Model
{
    use SoftDeletes;

    protected $table = 'zoom_tokens';
    
    protected $fillable = [
        'user_id',
        'account_id',
        'access_token',
        'refresh_token',
        'expires_at',
        'token_type',
        'scopes',
        'auth_type',
        'app_type',   // 'user' = per-user managed app | 'admin' = admin-managed app
        'zoom_email',
        'zoom_name',
        'zoom_user_id',
        'zoom_extension',
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

    /**
     * Scope for admin-managed app tokens only.
     */
    public function scopeAdminApp($query)
    {
        return $query->where('app_type', 'admin');
    }

    /**
     * Scope for user-managed app tokens only.
     */
    public function scopeUserApp($query)
    {
        return $query->where(function ($q) {
            $q->where('app_type', 'user')->orWhereNull('app_type');
        });
    }

    /**
     * Get the user that owns the token.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
