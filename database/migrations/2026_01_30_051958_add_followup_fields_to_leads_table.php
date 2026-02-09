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
            $table->unsignedBigInteger('assigned_followup_person')->nullable()->after('issuance_reason');
            $table->foreign('assigned_followup_person')->references('id')->on('users')->onDelete('set null');
            $table->enum('followup_status', ['Yes', 'No'])->default('No')->after('assigned_followup_person');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['assigned_followup_person']);
            $table->dropColumn(['assigned_followup_person', 'followup_status']);
        });
    }
};
