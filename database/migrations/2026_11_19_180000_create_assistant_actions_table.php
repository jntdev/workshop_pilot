<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Actions que l'assistant propose (changer un statut, marquer une pièce, déplacer un rendez-vous).
     * Rien n'est modifié tant que l'utilisateur n'a pas confirmé : cette table est la proposition,
     * puis le journal de ce qui a été fait (avant, après, qui, quand).
     */
    public function up(): void
    {
        Schema::create('assistant_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assistant_message_id')->constrained('assistant_messages')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->uuid('conversation_id')->index();
            $table->string('type', 30);
            $table->string('subject_type', 30);
            $table->unsignedBigInteger('subject_id');
            $table->json('payload');
            $table->json('before');
            $table->json('after')->nullable();
            $table->text('summary');
            $table->json('warnings')->nullable();
            $table->string('status', 20)->default('proposed')->index();
            $table->string('error')->nullable();
            $table->timestamp('expires_at');
            $table->timestamp('confirmed_at')->nullable();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('undone_at')->nullable();
            $table->timestamps();

            $table->index(['subject_type', 'subject_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assistant_actions');
    }
};
