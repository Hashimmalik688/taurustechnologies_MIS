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
        // Make title nullable
        Schema::table('community_announcements', function (Blueprint $table) {
            $table->string('title', 200)->nullable()->change();
        });
        
        // Update priority enum to include 'normal'
        DB::statement("ALTER TABLE `community_announcements` MODIFY `priority` ENUM('info', 'normal', 'warning', 'urgent') DEFAULT 'normal'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert title to not nullable (but set empty titles to 'Announcement' first)
        DB::table('community_announcements')
            ->whereNull('title')
            ->orWhere('title', '')
            ->update(['title' => 'Announcement']);
            
        Schema::table('community_announcements', function (Blueprint $table) {
            $table->string('title', 200)->nullable(false)->change();
        });
        
        // Set 'normal' priorities to 'info' before reverting enum
        DB::table('community_announcements')
            ->where('priority', 'normal')
            ->update(['priority' => 'info']);
            
        // Revert priority enum
        DB::statement("ALTER TABLE `community_announcements` MODIFY `priority` ENUM('info', 'warning', 'urgent') DEFAULT 'info'");
    }
};
