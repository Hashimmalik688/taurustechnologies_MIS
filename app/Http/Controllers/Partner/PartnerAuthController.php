<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartnerAuthController extends Controller
{
    /**
     * Show partner login form
     */
    public function showLoginForm()
    {
        return view('partner.auth.login');
    }

    /**
     * Handle partner login attempt
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Clear any existing user session first to prevent conflicts
        if (Auth::check()) {
            Auth::logout();
        }

        // Clear any existing partner session
        if (Auth::guard('partner')->check()) {
            Auth::guard('partner')->logout();
        }

        // Regenerate session ID for security
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->regenerate();

        // Clear intended URL from previous session
        $request->session()->forget('url.intended');

        // Attempt to authenticate partner
        if (Auth::guard('partner')->attempt([
            'email' => $request->email,
            'password' => $request->password,
            'is_active' => true, // Only allow active partners
        ], $request->boolean('remember'))) {
            
            // Regenerate session after successful login
            $request->session()->regenerate();

            return redirect()->intended(route('partner.dashboard'));
        }

        // Return validation error with input preserved
        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'These credentials do not match our records or your account is inactive.']);
    }

    /**
     * Logout partner
     */
    public function logout(Request $request)
    {
        Auth::guard('partner')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('partner.login');
    }
}
