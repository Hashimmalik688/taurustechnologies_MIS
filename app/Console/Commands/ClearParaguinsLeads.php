<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Lead;

class ClearParaguinsLeads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leads:clear-paraguins';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all leads for paraguins team to start fresh';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for paraguins leads...');
        
        $count = Lead::where('team', 'paraguins')->count();
        
        if ($count === 0) {
            $this->info('No paraguins leads found.');
            return 0;
        }
        
        $this->info("Found {$count} paraguins leads.");
        
        if ($this->confirm('Do you want to delete all paraguins leads?', true)) {
            Lead::where('team', 'paraguins')->delete();
            $this->info("Successfully deleted {$count} paraguins leads.");
        } else {
            $this->info('Operation cancelled.');
        }
        
        return 0;
    }
}
