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
            if (!Schema::hasColumn('leads', 'account_title')) {
                $table->string('account_title')->nullable()->after('bank_name');
            }
            if (!Schema::hasColumn('leads', 'policy_number')) {
                $table->string('policy_number')->nullable()->after('policy_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if (Schema::hasColumn('leads', 'account_title')) {
                $table->dropColumn('account_title');
            }
            if (Schema::hasColumn('leads', 'policy_number')) {
                $table->dropColumn('policy_number');
            }
        });
    }
};
