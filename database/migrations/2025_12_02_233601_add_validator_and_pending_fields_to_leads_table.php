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
            // Add columns safely without relying on specific column positions
            if (!Schema::hasColumn('leads', 'assigned_validator_id')) {
                $table->unsignedBigInteger('assigned_validator_id')->nullable();
            }
            if (!Schema::hasColumn('leads', 'pending_reason')) {
                $table->string('pending_reason')->nullable();
            }
            if (!Schema::hasColumn('leads', 'account_number')) {
                $table->string('account_number')->nullable();
            }
        });

        // Add foreign key if table has assigned_validator_id
        Schema::table('leads', function (Blueprint $table) {
            if (Schema::hasColumn('leads', 'assigned_validator_id')) {
                try {
                    $table->foreign('assigned_validator_id')->references('id')->on('users')->onDelete('set null');
                } catch (\Exception $e) {
                    // Foreign key might already exist
                }
            }
        });
        
        // Update status enum to include 'forwarded'
        try {
            DB::statement("ALTER TABLE leads MODIFY COLUMN status ENUM('pending', 'transferred', 'closed', 'sale', 'accepted', 'rejected', 'underwritten', 'forwarded') DEFAULT 'pending'");
        } catch (\Exception $e) {
            // Column might not exist yet
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['assigned_validator_id']);
            $table->dropColumn(['assigned_validator_id', 'pending_reason', 'account_number']);
        });
    }
};
