<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tracks which closer dialed which lead and when.
     * Prevents duplicate entries and allows per-user color coding.
     */
    public function up(): void
    {
        Schema::create('lead_dials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamp('dialed_at');
            $table->string('outcome', 50)->default('dialed'); // dialed, no_answer, callback, connected, not_interested
            $table->text('notes')->nullable();
            $table->timestamps();

            // Each user can only have one active dial record per lead (latest wins via upsert)
            $table->unique(['lead_id', 'user_id']);
            
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->index(['lead_id']);
            $table->index(['user_id', 'dialed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_dials');
    }
};
