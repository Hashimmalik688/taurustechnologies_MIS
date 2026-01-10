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
        // Set last_read_at to NOW for all participants where it's NULL
        // This ensures that only NEW messages (after this migration) will be counted as unread
        DB::table('chat_participants')
            ->whereNull('last_read_at')
            ->update(['last_read_at' => DB::raw('NOW()')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to NULL for rollback (optional)
        DB::table('chat_participants')
            ->update(['last_read_at' => null]);
    }
};
