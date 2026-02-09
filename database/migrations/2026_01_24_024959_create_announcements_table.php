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
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->text('message');
            $table->enum('animation', ['slide', 'fade', 'bounce', 'wave'])->default('slide');
            $table->enum('background_color', ['red', 'yellow', 'blue', 'green', 'purple', 'orange'])->default('blue');
            $table->enum('icon', ['warning', 'info', 'important', 'star', 'check', 'alert'])->default('info');
            $table->enum('auto_dismiss', ['never', '5s', '10s', '30s'])->default('never');
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index('is_active');
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
