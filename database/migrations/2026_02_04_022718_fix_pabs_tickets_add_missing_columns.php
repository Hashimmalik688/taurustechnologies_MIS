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
        if (Schema::hasTable('pabs_tickets')) {
            Schema::table('pabs_tickets', function (Blueprint $table) {
                // Add missing columns if they don't exist
                if (!Schema::hasColumn('pabs_tickets', 'quote_amount')) {
                    $table->decimal('quote_amount', 12, 2)->nullable()->after('total_cost');
                }
                if (!Schema::hasColumn('pabs_tickets', 'approval_status')) {
                    $table->enum('approval_status', ['PENDING', 'APPROVED', 'REJECTED'])->default('PENDING')->after('quote_amount');
                }
                if (!Schema::hasColumn('pabs_tickets', 'approved_at')) {
                    $table->timestamp('approved_at')->nullable()->after('approval_status');
                }
                if (!Schema::hasColumn('pabs_tickets', 'approval_notes')) {
                    $table->text('approval_notes')->nullable()->after('approved_at');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pabs_tickets')) {
            Schema::table('pabs_tickets', function (Blueprint $table) {
                if (Schema::hasColumn('pabs_tickets', 'quote_amount')) {
                    $table->dropColumn('quote_amount');
                }
                if (Schema::hasColumn('pabs_tickets', 'approval_status')) {
                    $table->dropColumn('approval_status');
                }
                if (Schema::hasColumn('pabs_tickets', 'approved_at')) {
                    $table->dropColumn('approved_at');
                }
                if (Schema::hasColumn('pabs_tickets', 'approval_notes')) {
                    $table->dropColumn('approval_notes');
                }
            });
        }
    }
};
