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
            // Add retained_at timestamp to track when sale was retained
            $table->timestamp('retained_at')->nullable()->after('retention_status');
        });

        // Fix the retention_status enum to match controller usage
        DB::statement("ALTER TABLE leads MODIFY COLUMN retention_status ENUM('pending', 'retained', 'lost') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('retained_at');
        });

        // Revert to old enum values
        DB::statement("ALTER TABLE leads MODIFY COLUMN retention_status ENUM('Yet to retain', 'chargeback', 'Sale:Retained', 'Sale:Rewrite') NULL");
    }
};
