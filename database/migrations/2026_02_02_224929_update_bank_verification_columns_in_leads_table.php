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
            // Rename bank_verification_reason to bank_verification_comment
            $table->renameColumn('bank_verification_reason', 'bank_verification_comment');
            
            // Drop the bank_verification_confirm_status column
            $table->dropColumn('bank_verification_confirm_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->renameColumn('bank_verification_comment', 'bank_verification_reason');
            $table->enum('bank_verification_confirm_status', ['Yes', 'No'])->default('No')->after('bank_verification_comment');
        });
    }
};
