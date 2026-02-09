<?php

namespace App\Console\Commands;

use App\Models\Lead;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeduplicateLeads extends Command
{
    protected $signature = 'leads:deduplicate';
    protected $description = 'Deduplicate leads by phone number (ALWAYS excludes peregrine team)';

    public function handle()
    {
        $this->info('Starting lead deduplication by phone number...');
        $this->warn('NOTE: Peregrine team leads are ALWAYS excluded from deduplication.');
        
        // ALWAYS exclude peregrine team - they must be protected at all costs
        $query = Lead::select('phone_number', DB::raw('COUNT(*) as count'))
            ->whereNotNull('phone_number')
            ->where('phone_number', '!=', '')
            ->where(function($q) {
                $q->where('team', '!=', 'peregrine')
                  ->orWhereNull('team');
            });
        
        $dupePhones = $query->groupBy('phone_number')
            ->having('count', '>', 1)
            ->get();

        $this->info("Found {$dupePhones->count()} duplicate phone numbers");

        $merged = 0;
        $deleted = 0;

        foreach ($dupePhones as $dupePhone) {
            // ALWAYS exclude peregrine team from deduplication
            $leads = Lead::where('phone_number', $dupePhone->phone_number)
                ->where(function($q) {
                    $q->where('team', '!=', 'peregrine')
                      ->orWhereNull('team');
                })
                ->orderBy('id', 'asc')
                ->get();
            
            if ($leads->count() < 2) {
                continue;
            }

            $this->line("Processing phone: {$dupePhone->phone_number} ({$leads->count()} duplicates)");

            $primary = $leads->first();
            $duplicates = $leads->slice(1);

            foreach ($duplicates as $dup) {
                // Merge data into primary
                foreach ($dup->getAttributes() as $key => $value) {
                    if (empty($primary->$key) && !empty($value)) {
                        $primary->$key = $value;
                    }
                }
                $primary->save();
                
                // Delete duplicate
                $dup->delete();
                $deleted++;
            }
            $merged++;
        }

        $this->info("Deduplication complete!");
        $this->info("Duplicate phone numbers processed: {$merged}");
        $this->info("Duplicate leads deleted: {$deleted}");

        return 0;
    }
}
