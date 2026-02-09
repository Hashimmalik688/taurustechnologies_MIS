<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to prevent Partners from accessing User/Employee areas
 * Partners should only access their own dashboard via the partner guard
 */
class PreventPartnerAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If a partner is logged in via the partner guard, prevent access to user areas
        if (Auth::guard('partner')->check()) {
            Auth::guard('partner')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('partner.login')
                ->withErrors(['error' => 'Partners cannot access this area. Please use the partner portal.']);
        }

        return $next($request);
    }
}
