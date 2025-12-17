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
            $table->text('card_number')->nullable()->change();
            $table->text('cvv')->nullable()->change();
            $table->text('ssn')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('card_number')->nullable()->change();
            $table->string('cvv', 4)->nullable()->change();
            $table->string('ssn')->nullable()->change();
        });
    }
};
