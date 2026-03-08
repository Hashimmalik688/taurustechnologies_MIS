<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One row per lead that is currently being called.
     * locked_at + TTL_MINUTES (on the model) determines expiry.
     */
    public function up(): void
    {
        Schema::create('lead_locks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->unique()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('locked_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_locks');
    }
};
