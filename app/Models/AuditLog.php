<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $table = 'audit_logs';

    protected $fillable = [
        'user_id',
        'user_email',
        'action',
        'model',
        'model_id',
        'ip_address',
        'user_agent',
        'changes',
        'description',
    ];

    protected $casts = [
        'changes' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who performed the action
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log an action to the audit log
     */
    public static function logAction(
        string $action,
        ?User $user = null,
        ?string $model = null,
        ?int $model_id = null,
        ?array $changes = null,
        ?string $description = null
    ): void {
        self::create([
            'user_id' => $user?->id,
            'user_email' => $user?->email ?? 'System',
            'action' => $action,
            'model' => $model,
            'model_id' => $model_id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'changes' => $changes,
            'description' => $description,
        ]);
    }
}
