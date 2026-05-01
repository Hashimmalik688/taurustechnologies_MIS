<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->unsignedBigInteger('reply_to_id')->nullable()->after('forwarded_from_user_name');
            $table->boolean('is_pinned')->default(false)->after('reply_to_id');
            $table->unsignedBigInteger('pinned_by')->nullable()->after('is_pinned');

            $table->foreign('reply_to_id')->references('id')->on('chat_messages')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropForeign(['reply_to_id']);
            $table->dropColumn(['reply_to_id', 'is_pinned', 'pinned_by']);
        });
    }
};
