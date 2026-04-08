<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->unsignedBigInteger('chargeback_marked_by_id')->nullable()->after('chargeback_marked_date');
            $table->timestamp('chargeback_paid_at')->nullable()->after('chargeback_marked_by_id');
            $table->unsignedBigInteger('chargeback_paid_by_id')->nullable()->after('chargeback_paid_at');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['chargeback_marked_by_id', 'chargeback_paid_at', 'chargeback_paid_by_id']);
        });
    }
};
