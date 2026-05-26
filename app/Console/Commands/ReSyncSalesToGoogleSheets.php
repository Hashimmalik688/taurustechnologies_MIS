<?php

namespace App\Console\Commands;

use App\Jobs\SyncPeregrineSaleToGoogleSheets;
use App\Jobs\SyncSaleToGoogleSheets;
use App\Models\Lead;
use Illuminate\Console\Command;

class ReSyncSalesToGoogleSheets extends Command
{
    protected $signature = 'sales:resync-google-sheets
                            {--date= : Resync sales for a specific date (Y-m-d)}
                            {--since= : Resync sales from this date onwards (Y-m-d)}
                            {--lead-id= : Resync a specific lead by ID}
                            {--dry-run : Show what would be synced without actually doing it}';

    protected $description = 'Re-sync sales that failed or never made it to Google Sheets';

    public function handle(): int
    {
        $query = Lead::whereNotNull('sale_at')
            ->whereNull('google_sheet_synced_at');

        if ($this->option('lead-id')) {
            $query->where('id', $this->option('lead-id'));
        }

        if ($this->option('date')) {
            $query->whereDate('sale_at', $this->option('date'));
        }

        if ($this->option('since')) {
            $query->whereDate('sale_at', '>=', $this->option('since'));
        }

        $leads = $query->get();

        if ($leads->isEmpty()) {
            $this->info('No unsynced sales found.');
            return self::SUCCESS;
        }

        $this->info("Found {$leads->count()} unsynced sales:");
        $this->table(
            ['ID', 'Customer', 'Closer', 'Sale Date', 'Team'],
            $leads->map(fn ($l) => [$l->id, $l->cn_name, $l->closer_name, $l->sale_at, $l->team ?? 'N/A'])
        );

        if ($this->option('dry-run')) {
            $this->warn('Dry run - no sales were actually synced.');
            return self::SUCCESS;
        }

        if (!$this->confirm('Proceed with syncing these sales?')) {
            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($leads->count());
        $bar->start();

        foreach ($leads as $lead) {
            if ($lead->team === 'peregrines') {
                SyncPeregrineSaleToGoogleSheets::dispatch($lead);
            } else {
                SyncSaleToGoogleSheets::dispatch($lead);
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Dispatched {$leads->count()} sync jobs. Check logs for results.");

        return self::SUCCESS;
    }
}
