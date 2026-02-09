<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Split roles by pipe and check if user has any of them
        $roles = explode('|', $role);
        $hasRole = false;
        $userRoles = Auth::user()->getRoleNames()->toArray();
        
        \Log::info('EMS Role Check', [
            'user_id' => Auth::id(),
            'user_roles' => $userRoles,
            'required_roles' => $roles,
        ]);
        
        foreach ($roles as $singleRole) {
            if (Auth::user()->hasRole(trim($singleRole))) {
                $hasRole = true;
                break;
            }
        }

        if (!$hasRole) {
            \Log::warning('EMS Access Denied', [
                'user_id' => Auth::id(),
                'user_roles' => $userRoles,
                'required_roles' => $roles,
            ]);
            abort(403, 'Unauthorized action. You do not have permission to access this resource.');
        }

        return $next($request);
    }
}
