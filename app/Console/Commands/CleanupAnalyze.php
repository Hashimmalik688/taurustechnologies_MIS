<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CleanupAnalyze extends Command
{
    protected $signature = 'cleanup:analyze {--json : Output machine-readable JSON}';

    protected $description = 'Analyze project for potential dead controllers, unused views, and risky files.';

    public function handle(): int
    {
        $report = [
            'unreferenced_controllers' => $this->findUnreferencedControllers(),
            'dangling_views' => $this->findDanglingViews(),
            'suspicious_large_files' => $this->findSuspiciousLargeFiles(),
        ];

        if ($this->option('json')) {
            $this->line(json_encode($report, JSON_PRETTY_PRINT));
        } else {
            $this->info('📋 Cleanup Analysis Report');
            $this->section('Unreferenced Controllers (heuristic)', $report['unreferenced_controllers']);
            $this->section('Dangling Blade Views (heuristic)', $report['dangling_views']);
            $this->section('Suspicious Large Files (> 5 MB)', $report['suspicious_large_files']);
            $this->line('Note: This is heuristic-only. Review before deleting anything.');
        }

        return self::SUCCESS;
    }

    protected function section(string $title, array $items): void
    {
        $this->line('');
        $this->line("• {$title}");
        if (empty($items)) {
            $this->line('  - none');

            return;
        }
        foreach ($items as $i) {
            $this->line('  - '.$i);
        }
    }

    protected function findUnreferencedControllers(): array
    {
        $controllers = [];
        $controllerPath = app_path('Http/Controllers');
        if (File::isDirectory($controllerPath)) {
            foreach (File::allFiles($controllerPath) as $file) {
                if ($file->getExtension() === 'php') {
                    $controllers[] = $file->getFilenameWithoutExtension();
                }
            }
        }

        $routesText = '';
        $routesDir = base_path('routes');
        if (File::isDirectory($routesDir)) {
            foreach (File::allFiles($routesDir) as $file) {
                if ($file->getExtension() === 'php') {
                    $routesText .= File::get($file);
                }
            }
        }

        $unref = [];
        foreach ($controllers as $c) {
            // if class basename not mentioned in any route file, flag it
            if (stripos($routesText, $c.'::class') === false && stripos($routesText, $c.'@') === false) {
                $unref[] = "App\\Http\\Controllers\\{$c}";
            }
        }

        return $unref;
    }

    protected function findDanglingViews(): array
    {
        // collect all blade views as dot-notation keys
        $views = [];
        $viewsDir = resource_path('views');
        if (File::isDirectory($viewsDir)) {
            foreach (File::allFiles($viewsDir) as $file) {
                if ($file->getExtension() === 'php' || $file->getExtension() === 'blade.php') {
                    $rel = str_replace($viewsDir.DIRECTORY_SEPARATOR, '', $file->getRealPath());
                    $rel = preg_replace('/\.blade\.php$|\.php$/', '', $rel);
                    $views[] = str_replace(DIRECTORY_SEPARATOR, '.', $rel);
                }
            }
        }

        // search references via view('...') / View::make('...')
        $code = '';
        foreach ([app_path(), base_path('routes'), base_path('resources')] as $scan) {
            if (File::isDirectory($scan)) {
                foreach (File::allFiles($scan) as $f) {
                    if (in_array($f->getExtension(), ['php', 'vue', 'js', 'ts', 'tsx', 'blade.php'])) {
                        $code .= File::get($f);
                    }
                }
            }
        }

        $dangling = [];
        foreach ($views as $v) {
            // match patterns view('x.y'), View::make('x.y'), @include('x.y'), @extends('x.y')
            if (! preg_match("/(view|View::make|@include|@extends)\(['\"]".preg_quote($v, '/')."['\"]\)/", $code)) {
                $dangling[] = $v;
            }
        }

        return $dangling;
    }

    protected function findSuspiciousLargeFiles(): array
    {
        $hits = [];
        $limit = 5 * 1024 * 1024; // 5MB
        foreach ([base_path('storage'), base_path('public'), base_path('resources')] as $dir) {
            if (\Illuminate\Support\Facades\File::isDirectory($dir)) {
                foreach (File::allFiles($dir) as $f) {
                    if ($f->getSize() >= $limit) {
                        $hits[] = str_replace(base_path().DIRECTORY_SEPARATOR, '', $f->getPathname()).' ('.round($f->getSize() / 1024 / 1024, 2).' MB)';
                    }
                }
            }
        }

        return $hits;
    }
}
