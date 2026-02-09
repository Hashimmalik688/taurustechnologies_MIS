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
        Schema::create('bad_leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->nullable()->constrained('leads')->onDelete('set null');
            $table->foreignId('disposed_by')->constrained('users')->onDelete('cascade');
            $table->string('disposition'); // 'no_answer', 'wrong_number', 'wrong_details'
            $table->text('notes')->nullable();
            $table->string('lead_name')->nullable();
            $table->string('lead_phone')->nullable();
            $table->string('lead_ssn')->nullable();
            $table->timestamps();
            
            $table->index(['disposition', 'created_at']);
            $table->index('disposed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bad_leads');
    }
};
