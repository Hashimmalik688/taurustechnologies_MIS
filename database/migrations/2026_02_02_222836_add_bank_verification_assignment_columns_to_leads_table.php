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
            $table->bigInteger('assigned_bank_verifier')->unsigned()->nullable()->after('assigned_followup_person');
            $table->foreign('assigned_bank_verifier')->references('id')->on('users')->onDelete('set null');
            $table->string('bank_verification_reason')->nullable()->after('bank_verification_notes');
            $table->enum('bank_verification_confirm_status', ['Yes', 'No'])->default('No')->after('bank_verification_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['assigned_bank_verifier']);
            $table->dropColumn(['assigned_bank_verifier', 'bank_verification_reason', 'bank_verification_confirm_status']);
        });
    }
};
