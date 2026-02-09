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
        Schema::table('leads', function (Blueprint $table) {
            $table->timestamp('disposed_at')->nullable()->after('sale_at');
            $table->foreignId('disposed_by')->nullable()->constrained('users')->onDelete('set null')->after('disposed_at');
            $table->enum('disposition_reason', ['no_answer', 'wrong_number', 'wrong_details'])->nullable()->after('disposed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['disposed_at', 'disposed_by', 'disposition_reason']);
        });
    }
};
