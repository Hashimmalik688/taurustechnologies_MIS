<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'In Review' to the qa_status enum (MySQL requires listing all values)
        DB::statement("ALTER TABLE `leads` MODIFY COLUMN `qa_status` ENUM('Pending','Good','Avg','Bad','In Review') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `leads` MODIFY COLUMN `qa_status` ENUM('Pending','Good','Avg','Bad') NULL");
    }
};
