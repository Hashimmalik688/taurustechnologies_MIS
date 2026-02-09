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
                $table->decimal('quote_amount', 12, 2)->nullable()->after('total_cost');
                $table->enum('approval_status', ['PENDING', 'APPROVED', 'REJECTED'])->default('PENDING')->after('quote_amount');
                $table->timestamp('approved_at')->nullable()->after('approval_status');
                $table->text('approval_notes')->nullable()->after('approved_at');
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
                $table->dropColumn(['quote_amount', 'approval_status', 'approved_at', 'approval_notes']);
            });
        }
    }
};
