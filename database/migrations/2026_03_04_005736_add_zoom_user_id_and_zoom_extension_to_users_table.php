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
        Schema::table('users', function (Blueprint $table) {
            $table->string('zoom_user_id')->nullable()->after('zoom_number')
                ->comment('Zoom user ID (e.g. Jr14svAdSXGsMrUywRSFsA) for webhook agent matching');
            $table->string('zoom_extension')->nullable()->after('zoom_user_id')
                ->comment('Zoom Phone extension number (e.g. 805)');

            $table->index('zoom_user_id');
            $table->index('zoom_extension');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['zoom_user_id']);
            $table->dropIndex(['zoom_extension']);
            $table->dropColumn(['zoom_user_id', 'zoom_extension']);
        });
    }
};
