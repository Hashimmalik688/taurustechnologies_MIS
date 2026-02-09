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
            // Issuance fields
            $table->string('issuance_status')->nullable();
            $table->timestamp('issuance_date')->nullable();
            $table->string('issuance_reason')->nullable();
            $table->unsignedBigInteger('issued_by')->nullable();
            $table->string('issuance_disposition')->nullable()->comment('Via Portal, Via Email, By Carrier, By Bank');
            $table->timestamp('issuance_disposition_date')->nullable();
            $table->unsignedBigInteger('disposition_officer_id')->nullable();
            $table->boolean('has_other_insurances')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn([
                'issuance_status',
                'issuance_disposition_date',
                'disposition_officer_id',
                'has_other_insurances',
                'issuance_disposition'
            ]);
        });
    }
};
