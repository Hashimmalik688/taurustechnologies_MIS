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
        Schema::table('users', function (Blueprint $table) {
            // Employee information
            $table->date('joining_date')->nullable()->after('email_verified_at');
            $table->date('termination_date')->nullable()->after('joining_date');
            $table->string('employee_id')->nullable()->unique()->after('termination_date');
            $table->string('designation')->nullable()->after('employee_id');
            $table->string('department')->nullable()->after('designation');
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'intern'])->default('full_time')->after('department');
            $table->enum('employment_status', ['active', 'inactive', 'on_leave', 'terminated'])->default('active')->after('employment_type');

            // Contact information
            $table->string('phone')->nullable()->after('zoom_number');
            $table->string('emergency_contact')->nullable()->after('phone');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact');

            // Banking information for payroll
            $table->string('bank_name')->nullable()->after('bonus_per_extra_sale');
            $table->string('account_number')->nullable()->after('bank_name');
            $table->string('routing_number')->nullable()->after('account_number');
            $table->string('account_holder_name')->nullable()->after('routing_number');

            // Additional information
            $table->text('address')->nullable()->after('account_holder_name');
            $table->string('city')->nullable()->after('address');
            $table->string('state')->nullable()->after('city');
            $table->string('zip_code')->nullable()->after('state');
            $table->string('country')->default('USA')->after('zip_code');

            // Add indexes
            $table->index('employee_id');
            $table->index('employment_status');
            $table->index('department');
            $table->index('joining_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['employee_id']);
            $table->dropIndex(['employment_status']);
            $table->dropIndex(['department']);
            $table->dropIndex(['joining_date']);

            $table->dropColumn([
                'joining_date',
                'termination_date',
                'employee_id',
                'designation',
                'department',
                'employment_type',
                'employment_status',
                'phone',
                'emergency_contact',
                'emergency_contact_phone',
                'bank_name',
                'account_number',
                'routing_number',
                'account_holder_name',
                'address',
                'city',
                'state',
                'zip_code',
                'country',
            ]);
        });
    }
};
