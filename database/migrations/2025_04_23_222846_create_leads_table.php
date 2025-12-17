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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('date')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('cn_name')->nullable();
            $table->string('date_of_birth')->nullable();
            $table->string('height_weight')->nullable();
            $table->string('birth_place')->nullable();
            $table->text('medical_issue')->nullable();
            $table->text('medications')->nullable();
            $table->string('doctor_name')->nullable();
            $table->string('ssn')->nullable();
            $table->text('address')->nullable();
            $table->string('carrier_name')->nullable();
            $table->decimal('coverage_amount', 15, 2)->nullable();
            $table->decimal('monthly_premium', 10, 2)->nullable();
            $table->string('beneficiary')->nullable();
            $table->string('smoker')->nullable();
            $table->string('policy_type')->nullable();
            $table->date('initial_draft_date')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_type')->nullable();
            $table->string('routing_number')->nullable();
            $table->string('account_verified_by')->nullable();
            $table->decimal('bank_balance', 15, 2)->nullable();
            $table->string('source')->nullable();
            $table->string('closer_name')->nullable();

            // Status fields
            $table->enum('status', ['pending', 'accepted', 'rejected', 'underwritten', 'forwarded'])->default('pending');
            $table->text('staff_notes')->nullable();
            $table->text('manager_notes')->nullable();

            $table->foreignId('forwarded_by')->nullable()->constrained('users');
            $table->foreignId('managed_by')->nullable()->constrained('users');

            $table->timestamp('sale_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Add indexes for foreign keys and frequently queried columns
            $table->index('forwarded_by');
            $table->index('managed_by');
            $table->index('status');
            $table->index('phone_number');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
