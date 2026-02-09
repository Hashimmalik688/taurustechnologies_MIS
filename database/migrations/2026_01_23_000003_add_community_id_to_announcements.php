<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('announcements')) {
            Schema::table('announcements', function (Blueprint $table) {
                // Add community_id foreign key
                $table->foreignId('community_id')->nullable()->constrained('communities')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('announcements')) {
            Schema::table('announcements', function (Blueprint $table) {
                $table->dropForeignKeyIfExists(['community_id']);
                $table->dropColumn('community_id');
            });
        }
    }
};
