<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('contact_info');
            $table->string('emergency_contact');
            $table->string('cnic');
            $table->string('position');
            $table->string('area_of_residence');
            $table->string('status');
            $table->string('mis');
            $table->string('passport_image')->nullable();
            $table->string('account_password')->nullable(); // Only visible to superadmin
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
