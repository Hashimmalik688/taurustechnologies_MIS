<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });

        // Strict rate limit for CRM login – 10 attempts per minute per IP
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip())->response(function () {
                return response()->json(
                    ['message' => 'Too many login attempts. Please wait before trying again.'],
                    429
                );
            });
        });

        // Strict rate limit for partner portal login
        RateLimiter::for('partner-login', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip())->response(function () {
                return redirect()->back()
                    ->withErrors(['email' => 'Too many login attempts. Please wait before trying again.']);
            });
        });

        // General web rate limit – protects all web routes from flooding
        RateLimiter::for('web', function (Request $request) {
            return Limit::perMinute(300)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
