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
        Schema::table('leads', function (Blueprint $table) {
            $table->string('bank_verification_status', 50)->nullable();
            $table->timestamp('bank_verification_date')->nullable();
            $table->text('bank_verification_notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn([
                'bank_verification_status',
                'bank_verification_date',
                'bank_verification_notes',
            ]);
        });
    }
};
