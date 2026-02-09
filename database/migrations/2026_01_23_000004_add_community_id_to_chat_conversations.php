<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('chat_conversations', function (Blueprint $table) {
            // Add community_id foreign key to link conversations to communities
            $table->foreignId('community_id')->nullable()->constrained('communities')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('chat_conversations', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['community_id']);
            $table->dropColumn('community_id');
        });
    }
};
