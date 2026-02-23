<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->boolean('commission_paid_to_partner')->default(false)->after('partner_set_at');
            $table->timestamp('commission_paid_at')->nullable()->after('commission_paid_to_partner');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['commission_paid_to_partner', 'commission_paid_at']);
        });
    }
};
