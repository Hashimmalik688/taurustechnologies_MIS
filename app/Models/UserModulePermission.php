<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserModulePermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'module_id',
        'permission_level',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'module_id' => 'integer',
    ];

    /**
     * Get the user that owns the permission
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the module that owns the permission
     */
    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Check if permission level allows viewing
     */
    public function canView(): bool
    {
        return in_array($this->permission_level, ['view', 'edit', 'full']);
    }

    /**
     * Check if permission level allows editing
     */
    public function canEdit(): bool
    {
        return in_array($this->permission_level, ['edit', 'full']);
    }

    /**
     * Check if permission level allows deleting
     */
    public function canDelete(): bool
    {
        return $this->permission_level === 'full';
    }

    /**
     * Get numeric permission level for comparison
     * none = 0, view = 1, edit = 2, full = 3
     */
    public function getNumericLevel(): int
    {
        $levels = [
            'none' => 0,
            'view' => 1,
            'edit' => 2,
            'full' => 3,
        ];

        return $levels[$this->permission_level] ?? 0;
    }

    /**
     * Scope to get overrides for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
