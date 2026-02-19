<?php

namespace App\Models;

use App\Support\PermissionLevel;
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
        return in_array($this->permission_level, [PermissionLevel::VIEW, PermissionLevel::EDIT, PermissionLevel::FULL]);
    }

    /**
     * Check if permission level allows editing
     */
    public function canEdit(): bool
    {
        return in_array($this->permission_level, [PermissionLevel::EDIT, PermissionLevel::FULL]);
    }

    /**
     * Check if permission level allows deleting
     */
    public function canDelete(): bool
    {
        return $this->permission_level === PermissionLevel::FULL;
    }

    /**
     * Get numeric permission level for comparison
     */
    public function getNumericLevel(): int
    {
        return PermissionLevel::numeric($this->permission_level);
    }

    /**
     * Scope to get overrides for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
