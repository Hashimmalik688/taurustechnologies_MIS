<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('failure_reason')->nullable()->after('status');
        });

        // Update status enum to include 'transferred', 'closed', and 'sale'
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE leads DROP CONSTRAINT IF EXISTS leads_status_check");
            DB::statement("ALTER TABLE leads ADD CONSTRAINT leads_status_check CHECK (status IN ('pending', 'transferred', 'closed', 'sale', 'accepted', 'rejected', 'underwritten', 'forwarded'))");
        } else {
            DB::statement("ALTER TABLE leads MODIFY COLUMN status ENUM('pending', 'transferred', 'closed', 'sale', 'accepted', 'rejected', 'underwritten', 'forwarded') DEFAULT 'pending'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('failure_reason');
        });

        // Revert status enum
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE leads DROP CONSTRAINT IF EXISTS leads_status_check");
            DB::statement("ALTER TABLE leads ADD CONSTRAINT leads_status_check CHECK (status IN ('pending', 'transferred', 'accepted', 'rejected', 'underwritten', 'forwarded'))");
        } else {
            DB::statement("ALTER TABLE leads MODIFY COLUMN status ENUM('pending', 'transferred', 'accepted', 'rejected', 'underwritten', 'forwarded') DEFAULT 'pending'");
        }
    }
};
