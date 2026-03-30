<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'cancelled_by_customer' to the not_issued_disposition ENUM
        DB::statement("ALTER TABLE leads MODIFY COLUMN not_issued_disposition ENUM(
            'email_missing',
            'ssn_missing',
            'postal_mail_missing',
            'beneficiary_incomplete',
            'doctor_info_missing',
            'underwriting_by_law',
            'cancelled_by_customer'
        ) NULL");

        // Also add to not_paid_manual_disposition ENUM (same values)
        DB::statement("ALTER TABLE leads MODIFY COLUMN not_paid_manual_disposition ENUM(
            'email_missing',
            'ssn_missing',
            'postal_mail_missing',
            'beneficiary_incomplete',
            'doctor_info_missing',
            'underwriting_by_law',
            'cancelled_by_customer'
        ) NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'cancelled_by_customer' from both ENUMs (revert to original)
        DB::statement("ALTER TABLE leads MODIFY COLUMN not_issued_disposition ENUM(
            'email_missing',
            'ssn_missing',
            'postal_mail_missing',
            'beneficiary_incomplete',
            'doctor_info_missing',
            'underwriting_by_law'
        ) NULL");

        DB::statement("ALTER TABLE leads MODIFY COLUMN not_paid_manual_disposition ENUM(
            'email_missing',
            'ssn_missing',
            'postal_mail_missing',
            'beneficiary_incomplete',
            'doctor_info_missing',
            'underwriting_by_law'
        ) NULL");
    }
};
