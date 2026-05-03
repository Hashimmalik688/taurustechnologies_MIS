<?php

namespace App\Console\Commands;

use App\Models\AllowedDevice;
use Illuminate\Console\Command;

class PurgePendingDevices extends Command
{
    protected $signature = 'device:purge-pending
                            {--hours=48 : Auto-reject pending devices older than this many hours}
                            {--dry-run  : Show what would be rejected without making changes}';

    protected $description = 'Auto-reject pending devices that have been waiting longer than --hours (default 48h)';

    public function handle(): int
    {
        $hours   = (int) $this->option('hours');
        $dryRun  = $this->option('dry-run');
        $cutoff  = now()->subHours($hours);

        $query = AllowedDevice::where('status', 'pending')
            ->where('created_at', '<', $cutoff);

        $count = $query->count();

        if ($count === 0) {
            $this->info("No pending devices older than {$hours}h found.");
            return 0;
        }

        if ($dryRun) {
            $this->warn("[dry-run] Would reject {$count} pending device(s) older than {$hours}h.");
            $query->get()->each(fn ($d) => $this->line("  • {$d->last_seen_ip}  ({$d->created_at})"));
            return 0;
        }

        $query->update(['status' => 'rejected']);
        $this->info("Auto-rejected {$count} pending device(s) older than {$hours}h.");

        return 0;
    }
}
