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
            $table->string('ravens_validation_status', 20)->nullable()->after('ravens_validated_by')
                  ->comment('valid or not_valid — set by Ravens Validator');
            $table->index('ravens_validation_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex(['ravens_validation_status']);
            $table->dropColumn('ravens_validation_status');
        });
    }
};
