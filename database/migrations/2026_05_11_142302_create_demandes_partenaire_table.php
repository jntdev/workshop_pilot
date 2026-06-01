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
        Schema::create('demandes_partenaire', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partenaire_id')->constrained('partenaires')->cascadeOnDelete();
            $table->date('date_debut');
            $table->date('date_fin');
            $table->json('velos_demandes');
            $table->text('commentaire')->nullable();
            $table->string('statut', 20)->default('en_attente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demandes_partenaire');
    }
};
