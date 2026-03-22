<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->enum('author_mode', ['comptoir', 'atelier']);
            $table->enum('recipient_mode', ['comptoir', 'atelier'])->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_phone', 50)->nullable();
            $table->string('contact_email')->nullable();
            $table->text('content');
            $table->enum('status', ['ouvert', 'resolu'])->default('ouvert');
            $table->timestamp('read_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'recipient_mode']);
            $table->index(['status', 'author_mode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
