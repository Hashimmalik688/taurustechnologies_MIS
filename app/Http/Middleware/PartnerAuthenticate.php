<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PartnerAuthenticate
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('partner')->check()) {
            // Clear any stored intended URL to prevent redirect to user login
            $request->session()->forget('url.intended');
            return redirect()->route('partner.login')
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Please login to access the partner dashboard.']);
        }

        // Check if partner is active
        if (!Auth::guard('partner')->user()->is_active) {
            Auth::guard('partner')->logout();
            $request->session()->forget('url.intended');
            return redirect()->route('partner.login')
                ->withErrors(['email' => 'Your account has been deactivated. Please contact support.']);
        }

        return $next($request);
    }
}
