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
        Schema::table('user_details', function (Blueprint $table) {
            // Add ssn_last4 if it doesn't exist
            if (!Schema::hasColumn('user_details', 'ssn_last4')) {
                $table->string('ssn_last4', 4)->nullable()->after('phone');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            if (Schema::hasColumn('user_details', 'ssn_last4')) {
                $table->dropColumn('ssn_last4');
            }
        });
    }
};
