<?php

namespace App\Http\Middleware;

use App\Services\PermissionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckModulePermission
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $module  Module slug (e.g., 'leads', 'ems', 'sales')
     * @param  string  $level   Required permission level (view|edit|full)
     */
    public function handle(Request $request, Closure $next, string $module, string $level = 'view'): Response
    {
        $user = $request->user();

        // If no user is authenticated, let auth middleware handle it
        if (!$user) {
            return $next($request);
        }

        // Check if user has required permission
        if (!$this->permissionService->hasPermission($user, $module, $level)) {
            // Log unauthorized access attempt
            \Log::warning('Unauthorized module access attempt', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'module' => $module,
                'required_level' => $level,
                'user_level' => $this->permissionService->getUserPermissionForModule($user, $module),
                'route' => $request->path(),
                'ip' => $request->ip(),
            ]);

            // Log to audit log if exists
            if (class_exists(\App\Models\AuditLog::class)) {
                \App\Models\AuditLog::create([
                    'user_id' => $user->id,
                    'action' => 'Access Denied',
                    'model' => 'Module Permission',
                    'model_id' => null,
                    'old_values' => null,
                    'new_values' => json_encode([
                        'module' => $module,
                        'required_level' => $level,
                        'user_level' => $this->permissionService->getUserPermissionForModule($user, $module),
                    ]),
                    'ip_address' => $request->ip(),
                ]);
            }

            // Return 403 Forbidden
            abort(403, "You don't have permission to {$level} this module.");
        }

        return $next($request);
    }
}
