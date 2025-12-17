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
        Schema::create('carriers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('policy_number')->nullable();
            $table->decimal('premium_amount', 10, 2)->nullable();
            $table->decimal('coverage_amount', 12, 2)->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->enum('status', ['pending', 'accepted', 'rejected', 'underwritten', 'forwarded'])->default('pending');
            $table->text('notes')->nullable();

            $table->foreignId('forwarded_by')->nullable()->constrained('users');
            $table->foreignId('managed_by')->nullable()->constrained('users');

            $table->timestamp('sale_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Add indexes for foreign keys and frequently queried columns
            $table->index('lead_id');
            $table->index('forwarded_by');
            $table->index('managed_by');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carriers');
    }
};
