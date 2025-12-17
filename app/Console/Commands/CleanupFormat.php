<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CleanupFormat extends Command
{
    protected $signature = 'cleanup:format {--fix-namespaces : Auto-fix PSR-4 namespaces and class names} {--dry-run}';

    protected $description = 'Auto-format code using Pint / PHP-CS-Fixer if available; optional namespace fixer.';

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');
        $this->info('🧹 Code Formatting');

        // Attempt Pint first
        $pint = base_path('vendor/bin/pint');
        if (File::exists($pint)) {
            $this->line('• Running Laravel Pint...');
            if (! $dry) {
                passthru(PHP_BINARY.' '.escapeshellarg($pint).' -v');
            } else {
                $this->line('  (dry-run) would run Pint');
            }
        } else {
            // Try php-cs-fixer
            $cs = base_path('vendor/bin/php-cs-fixer');
            if (File::exists($cs)) {
                $this->line('• Running PHP-CS-Fixer...');
                if (! $dry) {
                    passthru(PHP_BINARY.' '.escapeshellarg($cs).' fix --verbose');
                } else {
                    $this->line('  (dry-run) would run PHP-CS-Fixer');
                }
            } else {
                $this->warn('• Neither Pint nor PHP-CS-Fixer found. Skipping formatter.');
                $this->line('  Tip: composer require --dev laravel/pint');
            }
        }

        if ($this->option('fix-namespaces')) {
            $this->call('cleanup:psr4', ['--dry-run' => $dry]);
        }

        $this->info('✅ Formatting step done.');

        return self::SUCCESS;
    }
}
