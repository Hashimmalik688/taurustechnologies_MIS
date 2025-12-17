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
            // Retention tracking fields
            $table->enum('retention_status', [
                'Yet to retain', 
                'chargeback', 
                'Sale:Retained', 
                'Sale:Rewrite'
            ])->nullable()->after('status');
            
            $table->timestamp('chargeback_marked_date')->nullable()->after('retention_status');
            $table->boolean('is_rewrite')->default(false)->after('chargeback_marked_date');
            
            // Additional tracking
            $table->text('retention_notes')->nullable()->after('is_rewrite');
            $table->foreignId('retention_officer_id')->nullable()->after('retention_notes')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['retention_officer_id']);
            $table->dropColumn([
                'retention_status',
                'chargeback_marked_date',
                'is_rewrite',
                'retention_notes',
                'retention_officer_id',
            ]);
        });
    }
};
