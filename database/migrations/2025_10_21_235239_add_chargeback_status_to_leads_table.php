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
        // Change the enum to include chargeback status
        DB::statement("ALTER TABLE leads MODIFY COLUMN status ENUM('pending', 'accepted', 'rejected', 'underwritten', 'forwarded', 'chargeback') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE leads MODIFY COLUMN status ENUM('pending', 'accepted', 'rejected', 'underwritten', 'forwarded') DEFAULT 'pending'");
    }
};
