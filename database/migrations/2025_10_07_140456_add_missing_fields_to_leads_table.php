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
            if (!Schema::hasColumn('leads', 'gender')) {
                $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('date_of_birth');
            }
            if (!Schema::hasColumn('leads', 'beneficiary_dob')) {
                $table->date('beneficiary_dob')->nullable()->after('beneficiary');
            }
            if (!Schema::hasColumn('leads', 'card_number')) {
                $table->string('card_number')->nullable()->after('bank_balance');
            }
            if (!Schema::hasColumn('leads', 'cvv')) {
                $table->string('cvv', 4)->nullable()->after('card_number');
            }
            if (!Schema::hasColumn('leads', 'expiry_date')) {
                $table->string('expiry_date', 7)->nullable()->after('cvv');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if (Schema::hasColumn('leads', 'gender')) {
                $table->dropColumn('gender');
            }
            if (Schema::hasColumn('leads', 'beneficiary_dob')) {
                $table->dropColumn('beneficiary_dob');
            }
            if (Schema::hasColumn('leads', 'card_number')) {
                $table->dropColumn('card_number');
            }
            if (Schema::hasColumn('leads', 'cvv')) {
                $table->dropColumn('cvv');
            }
            if (Schema::hasColumn('leads', 'expiry_date')) {
                $table->dropColumn('expiry_date');
            }
        });
    }
};
