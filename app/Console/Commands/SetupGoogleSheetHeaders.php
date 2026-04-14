<?php

namespace App\Console\Commands;

use App\Services\GoogleSheetsService;
use Illuminate\Console\Command;

/**
 * One-time command to write the header row to the Google Sheet.
 *
 * Usage:
 *   php artisan sheets:setup-headers
 */
class SetupGoogleSheetHeaders extends Command
{
    protected $signature   = 'sheets:setup-headers';
    protected $description = 'Write Ravens Sales header row to the configured Google Sheet (safe to re-run — skips if headers already exist).';

    public function handle(GoogleSheetsService $sheets): int
    {
        $this->info('Writing header row to Google Sheet...');

        if ($sheets->writeHeaders()) {
            $this->info('Done! Headers are in place.');
            return self::SUCCESS;
        }

        $this->error('Failed — check storage/logs/laravel.log for details.');
        return self::FAILURE;
    }
}
