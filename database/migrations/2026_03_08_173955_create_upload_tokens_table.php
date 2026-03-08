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
        Schema::create('upload_tokens', function (Blueprint $table) {
            $table->id();
            $table->uuid('token')->unique();
            $table->string('context_type'); // 'message' ou 'message_reply'
            $table->unsignedBigInteger('context_id')->nullable(); // ID du message/reply (null si nouveau)
            $table->timestamp('expires_at');
            $table->unsignedInteger('used_count')->default(0);
            $table->unsignedInteger('max_uses')->default(10);
            $table->timestamps();

            $table->index(['token', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upload_tokens');
    }
};
