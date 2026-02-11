<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('zoom_tokens', function (Blueprint $table) {
            if (!Schema::hasColumn('zoom_tokens', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->onDelete('cascade');
                $table->index('user_id');
            }
            
            // Make account_id nullable since we're now using user_id for per-user OAuth
            if (Schema::hasColumn('zoom_tokens', 'account_id')) {
                $table->string('account_id')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zoom_tokens', function (Blueprint $table) {
            if (Schema::hasColumn('zoom_tokens', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
};
