<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Lead;

class ClearPeregrineLeads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leads:clear-peregrine';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all leads for peregrine team to start fresh';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for peregrine leads...');
        
        $count = Lead::where('team', 'peregrine')->count();
        
        if ($count === 0) {
            $this->info('No peregrine leads found.');
            return 0;
        }
        
        $this->info("Found {$count} peregrine leads.");
        
        if ($this->confirm('Do you want to delete all peregrine leads?', true)) {
            Lead::where('team', 'peregrine')->delete();
            $this->info("Successfully deleted {$count} peregrine leads.");
        } else {
            $this->info('Operation cancelled.');
        }
        
        return 0;
    }
}
