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
        Schema::create('zoom_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('account_id')->unique(); // Zoom account/org ID
            $table->longText('access_token'); // Encrypted access token
            $table->longText('refresh_token')->nullable(); // Encrypted refresh token
            $table->timestamp('expires_at')->nullable(); // When access token expires
            $table->string('token_type')->default('Bearer'); // Token type (Bearer, etc.)
            $table->text('scopes')->nullable(); // JSON scopes granted
            $table->string('auth_type')->default('server_to_server'); // 'oauth' or 'server_to_server'
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('account_id');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zoom_tokens');
    }
};
