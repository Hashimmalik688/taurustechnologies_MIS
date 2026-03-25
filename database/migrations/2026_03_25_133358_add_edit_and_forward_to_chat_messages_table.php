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
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->boolean('is_edited')->default(false)->after('type');
            $table->unsignedBigInteger('forwarded_from_message_id')->nullable()->after('is_edited');
            $table->string('forwarded_from_user_name')->nullable()->after('forwarded_from_message_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropColumn(['is_edited', 'forwarded_from_message_id', 'forwarded_from_user_name']);
        });
    }
};
