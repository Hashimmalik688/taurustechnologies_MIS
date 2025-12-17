<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Rename column from failure_reason to decline_reason
        Schema::table('leads', function (Blueprint $table) {
            $table->renameColumn('failure_reason', 'decline_reason');
        });
        
        // Update status values from rejected to declined
        DB::table('leads')
            ->where('status', 'rejected')
            ->update(['status' => 'declined']);
            
        // Update decline_reason values from Failed: to Declined:
        DB::table('leads')
            ->where('decline_reason', 'like', 'Failed:%')
            ->update([
                'decline_reason' => DB::raw("REPLACE(decline_reason, 'Failed:', 'Declined:')")
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert decline_reason values back to Failed:
        DB::table('leads')
            ->where('decline_reason', 'like', 'Declined:%')
            ->update([
                'decline_reason' => DB::raw("REPLACE(decline_reason, 'Declined:', 'Failed:')")
            ]);
            
        // Revert status values from declined to rejected
        DB::table('leads')
            ->where('status', 'declined')
            ->update(['status' => 'rejected']);
        
        // Rename column back from decline_reason to failure_reason
        Schema::table('leads', function (Blueprint $table) {
            $table->renameColumn('decline_reason', 'failure_reason');
        });
    }
};
