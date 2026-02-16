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
        Schema::create('user_module_permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('module_id');
            $table->enum('permission_level', ['none', 'view', 'edit', 'full'])->default('none');
            // User-specific permission overrides
            // These take precedence over role permissions
            // 'none' as override means explicitly deny access (even if role has permission)
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');

            // Unique constraint - one permission override per user per module
            $table->unique(['user_id', 'module_id']);

            // Indexes for faster lookups
            $table->index('user_id');
            $table->index('module_id');
            $table->index('permission_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_module_permissions');
    }
};
