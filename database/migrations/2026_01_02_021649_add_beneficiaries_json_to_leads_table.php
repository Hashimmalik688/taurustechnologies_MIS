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
            // Add beneficiaries JSON column to support multiple beneficiaries with DOB
            // Format: [{"name": "John Doe", "dob": "1990-01-15"}, {"name": "Jane Doe", "dob": "1992-05-20"}]
            if (!Schema::hasColumn('leads', 'beneficiaries')) {
                $table->json('beneficiaries')->nullable()->after('beneficiary_dob');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if (Schema::hasColumn('leads', 'beneficiaries')) {
                $table->dropColumn('beneficiaries');
            }
        });
    }
};
