<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Module;
use App\Models\User;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * Display the main permission management page (list of roles)
     */
    public function index()
    {
        $roles = Role::all();
        $modules = Module::active()->get();

        // Get permission counts for each role
        $roleStats = [];
        foreach ($roles as $role) {
            $permissions = $this->permissionService->getRolePermissions($role->id);
            $roleStats[$role->id] = [
                'total_modules' => $modules->count(),
                'full_access' => $permissions->where('permission_level', 'full')->count(),
                'edit_access' => $permissions->where('permission_level', 'edit')->count(),
                'view_access' => $permissions->where('permission_level', 'view')->count(),
                'no_access' => $modules->count() - $permissions->whereIn('permission_level', ['view', 'edit', 'full'])->count(),
            ];
        }

        return view('admin.permissions.index', compact('roles', 'roleStats', 'modules'));
    }

    /**
     * Show the permission matrix for a specific role
     */
    public function editRole(Role $role)
    {
        $modules = Module::active()->ordered()->get();
        $categories = Module::getCategories();

        // Group modules by category
        $modulesByCategory = [];
        foreach ($modules as $module) {
            $category = $module->category ?? 'Other';
            if (!isset($modulesByCategory[$category])) {
                $modulesByCategory[$category] = [];
            }
            $modulesByCategory[$category][] = $module;
        }

        // Get current permissions for this role
        $permissions = $this->permissionService->getRolePermissionMatrix($role->id);

        return view('admin.permissions.edit-role', compact('role', 'modulesByCategory', 'permissions', 'categories'));
    }

    /**
     * Update permissions for a specific role
     */
    public function updateRole(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'in:none,view,edit,full',
        ]);

        $oldPermissions = $this->permissionService->getRolePermissions($role->id)
            ->pluck('permission_level', 'module_id')
            ->toArray();

        // Update each permission
        foreach ($request->permissions as $moduleSlug => $level) {
            $this->permissionService->setRolePermission($role->id, $moduleSlug, $level);
        }

        // Log the change
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Updated Role Permissions',
            'model' => 'Role',
            'model_id' => $role->id,
            'old_values' => json_encode($oldPermissions),
            'new_values' => json_encode($request->permissions),
            'ip_address' => $request->ip(),
        ]);

        return redirect()
            ->route('settings.permissions.roles.edit', $role)
            ->with('success', "Permissions updated successfully for {$role->name} role.");
    }

    /**
     * Show the permission override matrix for a specific user
     */
    public function editUser(User $user)
    {
        $modules = Module::active()->ordered()->get();
        $categories = Module::getCategories();

        // Group modules by category
        $modulesByCategory = [];
        foreach ($modules as $module) {
            $category = $module->category ?? 'Other';
            if (!isset($modulesByCategory[$category])) {
                $modulesByCategory[$category] = [];
            }
            $modulesByCategory[$category][] = $module;
        }

        // Get current permissions for this user (showing inherited vs override)
        $permissions = $this->permissionService->getUserPermissionMatrix($user->id);

        // Get user's roles for display
        $userRoles = $user->roles->pluck('name')->toArray();

        return view('admin.permissions.edit-user', compact('user', 'modulesByCategory', 'permissions', 'categories', 'userRoles'));
    }

    /**
     * Update permission overrides for a specific user
     */
    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'in:none,view,edit,full,inherit',
        ]);

        $oldPermissions = $this->permissionService->getUserPermissions($user->id)
            ->pluck('permission_level', 'module_id')
            ->toArray();

        // Update each permission
        foreach ($request->permissions ?? [] as $moduleSlug => $level) {
            if ($level === 'inherit') {
                // Remove the override to inherit from role
                $this->permissionService->removeUserPermission($user->id, $moduleSlug);
            } else {
                // Set the override
                $this->permissionService->setUserPermission($user->id, $moduleSlug, $level);
            }
        }

        // Log the change
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Updated User Permission Overrides',
            'model' => 'User',
            'model_id' => $user->id,
            'old_values' => json_encode($oldPermissions),
            'new_values' => json_encode($request->permissions),
            'ip_address' => $request->ip(),
        ]);

        return redirect()
            ->route('settings.permissions.users.edit', $user)
            ->with('success', "Permission overrides updated successfully for {$user->name}.");
    }

    /**
     * AJAX endpoint for real-time permission updates
     */
    public function syncPermissions(Request $request)
    {
        $request->validate([
            'type' => 'required|in:role,user',
            'id' => 'required|integer',
            'module' => 'required|string',
            'level' => 'required|in:none,view,edit,full,inherit',
        ]);

        try {
            if ($request->type === 'role') {
                $role = Role::findOrFail($request->id);
                $this->permissionService->setRolePermission($role->id, $request->module, $request->level);
                
                AuditLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'Updated Permission (AJAX)',
                    'model' => 'Role',
                    'model_id' => $role->id,
                    'old_values' => null,
                    'new_values' => json_encode([
                        'module' => $request->module,
                        'level' => $request->level,
                    ]),
                    'ip_address' => $request->ip(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Permission updated successfully',
                ]);
            } else {
                $user = User::findOrFail($request->id);
                
                if ($request->level === 'inherit') {
                    $this->permissionService->removeUserPermission($user->id, $request->module);
                } else {
                    $this->permissionService->setUserPermission($user->id, $request->module, $request->level);
                }

                AuditLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'Updated Permission Override (AJAX)',
                    'model' => 'User',
                    'model_id' => $user->id,
                    'old_values' => null,
                    'new_values' => json_encode([
                        'module' => $request->module,
                        'level' => $request->level,
                    ]),
                    'ip_address' => $request->ip(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Permission override updated successfully',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update permission: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Clear all permission caches
     */
    public function clearCache(Request $request)
    {
        try {
            $this->permissionService->clearAllPermissionCaches();

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'Cleared Permission Cache',
                'model' => 'System',
                'model_id' => null,
                'old_values' => null,
                'new_values' => null,
                'ip_address' => $request->ip(),
            ]);

            return redirect()->back()->with('success', 'Permission cache cleared successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to clear cache: ' . $e->getMessage());
        }
    }
}
