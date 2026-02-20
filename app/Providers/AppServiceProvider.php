<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        Paginator::useBootstrapFive();

        // Register UserObserver
        \App\Models\User::observe(\App\Observers\UserObserver::class);

        // Force HTTPS in production
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // When using Ngrok, detect and use Ngrok URL
        if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
            $host = $_SERVER['HTTP_X_FORWARDED_HOST'];
            $schema = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'http';
            URL::forceRootUrl("$schema://$host");
        }

        // Register custom Blade directives for module permissions
        $this->registerPermissionBladeDirectives();
    }

    /**
     * Register custom Blade directives for module permission checks
     */
    protected function registerPermissionBladeDirectives()
    {
        // @canModule('leads', 'view') - Check if user can view a module
        \Blade::directive('canModule', function ($expression) {
            $parts = explode(',', str_replace(['(', ')', ' ', "'", '"'], '', $expression));
            $module = $parts[0] ?? '';
            $level = $parts[1] ?? 'view';
            
            return "<?php if(auth()->check() && auth()->user()->hasModulePermission('{$module}', '{$level}')): ?>";
        });

        \Blade::directive('endcanModule', function () {
            return '<?php endif; ?>';
        });

        // @cannotModule('leads', 'edit') - Check if user cannot edit a module
        \Blade::directive('cannotModule', function ($expression) {
            $parts = explode(',', str_replace(['(', ')', ' ', "'", '"'], '', $expression));
            $module = $parts[0] ?? '';
            $level = $parts[1] ?? 'view';
            
            return "<?php if(auth()->check() && !auth()->user()->hasModulePermission('{$module}', '{$level}')): ?>";
        });

        \Blade::directive('endcannotModule', function () {
            return '<?php endif; ?>';
        });

        // @canViewModule('leads') - Shorthand for view permission
        \Blade::directive('canViewModule', function ($expression) {
            $module = str_replace(['(', ')', ' ', "'", '"'], '', $expression);
            return "<?php if(auth()->check() && auth()->user()->canViewModule('{$module}')): ?>";
        });

        \Blade::directive('endcanViewModule', function () {
            return '<?php endif; ?>';
        });

        // @canEditModule('leads') - Shorthand for edit permission
        \Blade::directive('canEditModule', function ($expression) {
            $module = str_replace(['(', ')', ' ', "'", '"'], '', $expression);
            return "<?php if(auth()->check() && auth()->user()->canEditModule('{$module}')): ?>";
        });

        \Blade::directive('endcanEditModule', function () {
            return '<?php endif; ?>';
        });

        // @canDeleteInModule('leads') - Shorthand for full/delete permission
        \Blade::directive('canDeleteInModule', function ($expression) {
            $module = str_replace(['(', ')', ' ', "'", '"'], '', $expression);
            return "<?php if(auth()->check() && auth()->user()->canDeleteInModule('{$module}')): ?>";
        });

        \Blade::directive('endcanDeleteInModule', function () {
            return '<?php endif; ?>';
        });
    }
}
