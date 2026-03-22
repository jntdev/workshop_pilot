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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();

            // Dates
            $table->dateTime('date_contact');
            $table->date('date_reservation');
            $table->date('date_retour');

            // Logistique livraison
            $table->boolean('livraison_necessaire')->default(false);
            $table->text('adresse_livraison')->nullable();
            $table->string('contact_livraison')->nullable();
            $table->string('creneau_livraison')->nullable();

            // Logistique récupération
            $table->boolean('recuperation_necessaire')->default(false);
            $table->text('adresse_recuperation')->nullable();
            $table->string('contact_recuperation')->nullable();
            $table->string('creneau_recuperation')->nullable();

            // Finances
            $table->decimal('prix_total_ttc', 10, 2);
            $table->boolean('acompte_demande')->default(false);
            $table->decimal('acompte_montant', 10, 2)->nullable();
            $table->date('acompte_paye_le')->nullable();
            $table->date('paiement_final_le')->nullable();

            // Statut
            $table->enum('statut', ['reserve', 'en_attente_acompte', 'en_cours', 'paye', 'annule'])->default('reserve');
            $table->text('raison_annulation')->nullable();

            // Meta
            $table->text('commentaires')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
