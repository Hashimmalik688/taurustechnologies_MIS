<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Change 'mis' string column to enum with values 'Yes' and 'No'
            $table->enum('mis', ['Yes', 'No'])->default('Yes')->change();
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Revert back to string
            $table->string('mis')->change();
        });
    }
};
