<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds 'other_reason' to the not_issued_disposition ENUM and a new
     * not_issued_comment TEXT column for free-text input when the
     * "Other Reason" option is selected.
     */
    public function up(): void
    {
        // Add 'other_reason' to the not_issued_disposition ENUM
        DB::statement("ALTER TABLE leads MODIFY COLUMN not_issued_disposition ENUM(
            'email_missing',
            'ssn_missing',
            'postal_mail_missing',
            'beneficiary_incomplete',
            'doctor_info_missing',
            'underwriting_by_law',
            'cancelled_by_customer',
            'other_reason'
        ) NULL");

        // Add not_issued_comment column (only populated when disposition = other_reason)
        if (!Schema::hasColumn('leads', 'not_issued_comment')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->text('not_issued_comment')->nullable()->after('not_issued_disposition');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the comment column
        if (Schema::hasColumn('leads', 'not_issued_comment')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->dropColumn('not_issued_comment');
            });
        }

        // Revert ENUM back to previous 7 values
        DB::statement("ALTER TABLE leads MODIFY COLUMN not_issued_disposition ENUM(
            'email_missing',
            'ssn_missing',
            'postal_mail_missing',
            'beneficiary_incomplete',
            'doctor_info_missing',
            'underwriting_by_law',
            'cancelled_by_customer'
        ) NULL");
    }
};
