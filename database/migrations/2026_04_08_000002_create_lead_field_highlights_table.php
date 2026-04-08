<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_field_highlights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');
            $table->string('field_name', 100);
            $table->foreignId('updated_by_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('updated_at');

            // One highlight record per field per lead (upsert on edit)
            $table->unique(['lead_id', 'field_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_field_highlights');
    }
};
