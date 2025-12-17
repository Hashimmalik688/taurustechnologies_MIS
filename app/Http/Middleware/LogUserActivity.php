<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\AuditLog;

class LogUserActivity
{
    /**
     * Handle an incoming request - log user login on first request
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            // Check if user just logged in (first request with this session)
            if (!session()->has('activity_logged_for_session')) {
                $user->update([
                    'last_login_at' => now(),
                    'last_login_ip' => $request->ip(),
                    'current_session_ip' => $request->ip(),
                    'time_in' => now(),
                ]);

                // Log the login action
                AuditLog::logAction(
                    action: 'login',
                    user: $user,
                    model: 'User',
                    model_id: $user->id,
                    description: "User logged in from IP {$request->ip()}"
                );

                session()->put('activity_logged_for_session', true);
            }

            // Update last activity (for timeout functionality)
            session()->put('last_activity', now());
        }

        return $next($request);
    }
}
