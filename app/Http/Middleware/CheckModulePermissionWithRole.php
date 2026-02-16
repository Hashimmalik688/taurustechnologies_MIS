<?php

namespace App\Http\Middleware;

use App\Services\PermissionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckModulePermissionWithRole
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * Handle an incoming request with combined role and permission check.
     * This middleware allows routes to specify module permissions alongside roles.
     * 
     * Usage: ->middleware('role.permission:leads,edit')
     * This checks BOTH:
     * 1. User has permission level for the module
     * 2. Falls back to role-based access if permission not explicitly set
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $module  Module slug (e.g., 'leads', 'ems', 'sales')
     * @param  string  $level   Required permission level (view|edit|full)
     */
    public function handle(Request $request, Closure $next, string $module, string $level = 'view'): Response
    {
        $user = $request->user();

        // If no user authenticated, let auth middleware handle it
        if (!$user) {
            return $next($request);
        }

        // Check module permission
        $hasPermission = $this->permissionService->hasPermission($user, $module, $level);

        if (!$hasPermission) {
            // Log denied access
            \Log::warning('Module permission denied', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'module' => $module,
                'required_level' => $level,
                'user_level' => $this->permissionService->getUserPermissionForModule($user, $module),
                'route' => $request->path(),
                'ip' => $request->ip(),
            ]);

            // Log to audit
            if (class_exists(\App\Models\AuditLog::class)) {
                \App\Models\AuditLog::create([
                    'user_id' => $user->id,
                    'action' => 'Access Denied - Insufficient Permissions',
                    'model' => 'Module',
                    'model_id' => null,
                    'old_values' => null,
                    'new_values' => json_encode([
                        'module' => $module,
                        'required_level' => $level,
                        'user_level' => $this->permissionService->getUserPermissionForModule($user, $module),
                        'route' => $request->path(),
                    ]),
                    'ip_address' => $request->ip(),
                ]);
            }

            abort(403, "You don't have permission to {$level} this module. Required: {$level} access to {$module}");
        }

        return $next($request);
    }
}
