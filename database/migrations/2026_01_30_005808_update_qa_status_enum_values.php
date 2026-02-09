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
        // Step 1: Change column to VARCHAR to allow any value temporarily
        DB::statement("ALTER TABLE leads MODIFY COLUMN qa_status VARCHAR(50) DEFAULT NULL");
        
        // Step 2: Update existing data to map old values to new ones
        DB::statement("UPDATE leads SET qa_status = 'Pending' WHERE qa_status = 'In Review' OR qa_status IS NULL");
        DB::statement("UPDATE leads SET qa_status = 'Good' WHERE qa_status = 'Approved'");
        DB::statement("UPDATE leads SET qa_status = 'Bad' WHERE qa_status = 'Rejected'");
        
        // Step 3: Change column back to ENUM with new values
        DB::statement("ALTER TABLE leads MODIFY COLUMN qa_status ENUM('Pending', 'Good', 'Avg', 'Bad') DEFAULT 'Pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to old enum values
        DB::statement("ALTER TABLE leads MODIFY COLUMN qa_status ENUM('In Review', 'Approved', 'Rejected') DEFAULT 'In Review'");
        
        // Map new values back to old ones
        DB::statement("UPDATE leads SET qa_status = 'In Review' WHERE qa_status = 'Pending'");
        DB::statement("UPDATE leads SET qa_status = 'Approved' WHERE qa_status = 'Good'");
        DB::statement("UPDATE leads SET qa_status = 'Rejected' WHERE qa_status IN ('Bad', 'Avg')");
    }
};

