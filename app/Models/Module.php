<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'category',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get role permissions for this module
     */
    public function rolePermissions()
    {
        return $this->hasMany(RoleModulePermission::class);
    }

    /**
     * Get user permissions for this module
     */
    public function userPermissions()
    {
        return $this->hasMany(UserModulePermission::class);
    }

    /**
     * Get permission for a specific role
     */
    public function getPermissionForRole($roleId)
    {
        return $this->rolePermissions()
            ->where('role_id', $roleId)
            ->first();
    }

    /**
     * Get permission for a specific user
     */
    public function getPermissionForUser($userId)
    {
        return $this->userPermissions()
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Scope to get active modules only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get modules by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to order by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get all unique categories
     */
    public static function getCategories()
    {
        return self::whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->sort()
            ->values();
    }
}
