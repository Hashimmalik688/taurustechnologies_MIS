<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Get the post-login redirect path based on user role.
     *
     * @return string
     */
    protected function redirectTo()
    {
        $user = auth()->user();
        
        // Agents go to their dashboard
        if ($user->hasRole('Agent')) {
            return route('agent.dashboard');
        }
        
        // Verifiers should only access verifier form and chat
        if ($user->hasRole('Verifier')) {
            return route('verifier.dashboard');
        }
        
        // Paraguins Closers go to their dashboard
        if ($user->hasRole('Paraguins Closer')) {
            return route('paraguins.closers.index');
        }

        // Paraguins Validators go to validator dashboard
        if ($user->hasRole('Paraguins Validator')) {
            return route('validator.index');
        }
        
        // Managers can access validator dashboard too (they're also validators)
        if ($user->hasRole('Manager')) {
            return route('validator.index');
        }
        
        // Employees go to their dashboard
        if ($user->hasRole('Employee')) {
            return route('employee.dashboard');
        }
        
        // Everyone else goes to main dashboard
        return RouteServiceProvider::HOME;
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(\Illuminate\Http\Request $request)
    {
        // Make email case-insensitive by converting to lowercase
        return [
            'email' => strtolower($request->input($this->username())),
            'password' => $request->input('password'),
        ];
    }
}
