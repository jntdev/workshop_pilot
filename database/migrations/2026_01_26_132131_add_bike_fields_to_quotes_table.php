<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ajouter les colonnes avec des valeurs par défaut vides pour les devis existants
        Schema::table('quotes', function (Blueprint $table) {
            $table->string('bike_description')->default('')->after('client_id');
            $table->text('reception_comment')->nullable()->after('bike_description');
        });

        // Mettre à jour les enregistrements existants avec une chaîne vide
        DB::table('quotes')->whereNull('reception_comment')->update(['reception_comment' => '']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn(['bike_description', 'reception_comment']);
        });
    }
};
