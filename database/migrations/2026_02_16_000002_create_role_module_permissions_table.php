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
        Schema::create('role_module_permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('module_id');
            $table->enum('permission_level', ['none', 'view', 'edit', 'full'])->default('none');
            // none: No access
            // view: Read-only access
            // edit: Can view and modify (create/update)
            // full: Complete access (view/edit/delete)
            $table->timestamps();

            // Foreign keys
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');

            // Unique constraint - one permission per role per module
            $table->unique(['role_id', 'module_id']);

            // Indexes for faster lookups
            $table->index('role_id');
            $table->index('module_id');
            $table->index('permission_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_module_permissions');
    }
};
