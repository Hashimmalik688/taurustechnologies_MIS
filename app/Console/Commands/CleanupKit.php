<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CleanupKit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:run
                            {--dry-run : Show actions without making changes}
                            {--aggressive : Remove extra cruft like .DS_Store, Thumbs.db, *.orig, *.rej}
                            {--no-node : Skip node cache cleanup}
                            {--no-cache : Skip Laravel cache clears}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run project-wide cleanup: caches, logs, bootstrap cache, junk files, node cache.';

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');
        $aggressive = (bool) $this->option('aggressive');

        $this->info('🚀 Cleanup Kit (max) starting...');

        if (! $this->option('no-cache')) {
            $this->task('Clearing Laravel caches', function () use ($dry) {
                if (! $dry) {
                    $this->callSilent('cache:clear');
                    $this->callSilent('config:clear');
                    $this->callSilent('route:clear');
                    $this->callSilent('view:clear');
                }
            });
        }

        $this->task('Cleaning storage/logs', function () use ($dry) {
            $logPath = storage_path('logs');
            if (File::isDirectory($logPath)) {
                foreach (File::files($logPath) as $file) {
                    if (! $dry) {
                        File::delete($file);
                    }
                    $this->line('  - '.$file->getFilename());
                }
            }
        });

        $this->task('Removing bootstrap/cache/*.php compiled files', function () use ($dry) {
            $path = base_path('bootstrap/cache');
            if (File::isDirectory($path)) {
                foreach (File::files($path) as $file) {
                    if (str_ends_with($file->getFilename(), '.php')) {
                        if (! $dry) {
                            File::delete($file);
                        }
                        $this->line('  - '.$file->getFilename());
                    }
                }
            }
        });

        if (! $this->option('no-node')) {
            $this->task('Cleaning node_modules/.cache (if present)', function () use ($dry) {
                $cache = base_path('node_modules/.cache');
                if (File::exists($cache)) {
                    if (! $dry) {
                        File::deleteDirectory($cache);
                    }
                    $this->line('  - Removed node_modules/.cache');
                }
            });
        }

        $this->task('Removing temporary/junk files', function () use ($dry, $aggressive) {
            $globs = [
                base_path('**/*.log'),
                base_path('**/*.tmp'),
                base_path('**/*.temp'),
            ];
            if ($aggressive) {
                $globs = array_merge($globs, [
                    base_path('**/.DS_Store'),
                    base_path('**/Thumbs.db'),
                    base_path('**/*.orig'),
                    base_path('**/*.rej'),
                ]);
            }
            $deleted = 0;
            foreach ($globs as $pattern) {
                foreach (glob($pattern, GLOB_BRACE) as $file) {
                    if (is_file($file)) {
                        if (! $dry) {
                            @unlink($file);
                        }
                        $deleted++;
                        $this->line('  - '.str_replace(base_path().DIRECTORY_SEPARATOR, '', $file));
                    }
                }
            }
            $this->line("  -> matched {$deleted} files.");
        });

        $this->task('Composer autoload optimize', function () use ($dry) {
            if (! $dry) {
                @shell_exec('composer dump-autoload -o 2>&1');
            }
        });

        $this->info('✅ Cleanup complete.');
        $this->line('Tip: run php artisan cleanup:analyze for a safety report, and cleanup:format to auto-format code.');

        return self::SUCCESS;
    }

    protected function task(string $title, callable $callback): void
    {
        $this->line("• $title");
        $callback();
    }
}
