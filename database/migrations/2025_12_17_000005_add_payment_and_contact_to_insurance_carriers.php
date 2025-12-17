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
        Schema::table('insurance_carriers', function (Blueprint $table) {
            $table->enum('payment_module', ['on_draft', 'on_issue', 'as_earned'])->default('on_issue')->after('name');
            $table->string('phone')->nullable()->after('payment_module');
            $table->string('ssn_last4', 4)->nullable()->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('insurance_carriers', function (Blueprint $table) {
            $table->dropColumn(['payment_module', 'phone', 'ssn_last4']);
        });
    }
};
