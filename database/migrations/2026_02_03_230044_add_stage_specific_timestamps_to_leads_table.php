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
            // Add stage-specific timestamps to preserve when each person worked on the lead
            $table->timestamp('verified_at')->nullable()->after('verified_by')->comment('When verifier submitted the lead');
            $table->timestamp('closed_at')->nullable()->after('status')->comment('When closer sent to validator');
            $table->timestamp('validated_at')->nullable()->after('assigned_validator_id')->comment('When validator processed (approved/returned/declined)');
            $table->timestamp('returned_at')->nullable()->after('validated_at')->comment('When validator returned to closer');
            $table->timestamp('declined_at')->nullable()->after('returned_at')->comment('When lead was declined');
            $table->timestamp('transferred_at')->nullable()->after('declined_at')->comment('When lead was transferred to closer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn([
                'verified_at',
                'closed_at',
                'validated_at',
                'returned_at',
                'declined_at',
                'transferred_at'
            ]);
        });
    }
};
