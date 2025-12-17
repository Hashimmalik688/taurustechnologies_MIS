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
            if (!Schema::hasColumn('leads', 'driving_license')) {
                $table->string('driving_license', 100)->nullable()->after('smoker');
            }
            if (!Schema::hasColumn('leads', 'emergency_contact')) {
                $table->string('emergency_contact')->nullable()->after('beneficiary_dob');
            }
            if (!Schema::hasColumn('leads', 'future_draft_date')) {
                $table->date('future_draft_date')->nullable()->after('initial_draft_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if (Schema::hasColumn('leads', 'driving_license')) {
                $table->dropColumn('driving_license');
            }
            if (Schema::hasColumn('leads', 'emergency_contact')) {
                $table->dropColumn('emergency_contact');
            }
            if (Schema::hasColumn('leads', 'future_draft_date')) {
                $table->dropColumn('future_draft_date');
            }
        });
    }
};
