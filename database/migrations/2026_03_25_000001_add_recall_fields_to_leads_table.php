<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->timestamp('recall_requested_at')->nullable()->after('ravens_validation_status');
            $table->unsignedBigInteger('recall_requested_by')->nullable()->after('recall_requested_at');
            $table->text('recall_note')->nullable()->after('recall_requested_by');

            $table->foreign('recall_requested_by')->references('id')->on('users')->nullOnDelete();
            $table->index('recall_requested_at');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['recall_requested_by']);
            $table->dropIndex(['recall_requested_at']);
            $table->dropColumn(['recall_requested_at', 'recall_requested_by', 'recall_note']);
        });
    }
};
