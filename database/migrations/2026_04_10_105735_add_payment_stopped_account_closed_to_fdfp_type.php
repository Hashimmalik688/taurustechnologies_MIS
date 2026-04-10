<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add 'payment_stopped' and 'account_closed' to the not_paid_fdfp_type enum.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE leads MODIFY COLUMN not_paid_fdfp_type ENUM(
            'unstable_to_locate',
            'insufficient_fund',
            'unauthorized_payments',
            'manual_action',
            'payment_stopped',
            'account_closed'
        ) NULL");
    }

    /**
     * Reverse the migration — remove the two new values.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE leads MODIFY COLUMN not_paid_fdfp_type ENUM(
            'unstable_to_locate',
            'insufficient_fund',
            'unauthorized_payments',
            'manual_action'
        ) NULL");
    }
};
