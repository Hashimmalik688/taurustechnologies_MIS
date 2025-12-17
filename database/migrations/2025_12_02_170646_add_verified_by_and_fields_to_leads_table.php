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
            $table->unsignedBigInteger('verified_by')->nullable()->after('managed_by');
            $table->integer('age')->nullable()->after('date_of_birth');
            $table->string('state', 50)->nullable()->after('address');
            $table->string('zip_code', 10)->nullable()->after('state');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['verified_by', 'age', 'state', 'zip_code']);
        });
    }
};
