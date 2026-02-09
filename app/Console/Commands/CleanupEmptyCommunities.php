<?php

namespace App\Console\Commands;

use App\Models\Community;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupEmptyCommunities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'communities:cleanup-empty';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete communities that have no members';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Checking for empty communities...');
        
        $communities = Community::all();
        $deletedCount = 0;
        
        foreach ($communities as $community) {
            $memberCount = DB::table('community_members')
                ->where('community_id', $community->id)
                ->count();
            
            if ($memberCount == 0) {
                $this->warn("Deleting empty community: {$community->name} (ID: {$community->id})");
                $community->delete();
                $deletedCount++;
            }
        }
        
        if ($deletedCount > 0) {
            $this->info("✓ Deleted {$deletedCount} empty " . ($deletedCount == 1 ? 'community' : 'communities'));
        } else {
            $this->info('✓ No empty communities found');
        }
        
        return Command::SUCCESS;
    }
}
