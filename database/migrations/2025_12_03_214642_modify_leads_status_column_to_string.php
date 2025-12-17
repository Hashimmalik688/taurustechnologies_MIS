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
        // Change status from enum to string to accommodate longer values like 'returned'
        DB::statement('ALTER TABLE leads MODIFY COLUMN status VARCHAR(50) DEFAULT "pending"');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to enum
        DB::statement('ALTER TABLE leads MODIFY COLUMN status ENUM("pending", "accepted", "rejected", "underwritten", "forwarded") DEFAULT "pending"');
    }
};
