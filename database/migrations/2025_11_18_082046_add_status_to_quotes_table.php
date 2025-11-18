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
        // Étape 1: Mapper les anciennes valeurs vers les nouvelles temporairement dans une nouvelle colonne
        Schema::table('quotes', function (Blueprint $table) {
            $table->string('status_new')->after('status')->nullable();
        });

        DB::statement("UPDATE quotes SET status_new = 'brouillon' WHERE status = 'draft'");
        DB::statement("UPDATE quotes SET status_new = 'prêt' WHERE status = 'validated'");

        // Étape 2: Supprimer l'ancienne colonne et renommer la nouvelle
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('quotes', function (Blueprint $table) {
            $table->renameColumn('status_new', 'status');
        });

        // Étape 3: Convertir en enum avec les nouvelles valeurs
        DB::statement("ALTER TABLE quotes MODIFY COLUMN status ENUM('brouillon', 'prêt', 'modifiable', 'facturé') NOT NULL DEFAULT 'brouillon'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restaurer l'ancien enum
        DB::statement("ALTER TABLE quotes MODIFY COLUMN status ENUM('draft', 'validated') NOT NULL DEFAULT 'draft'");

        DB::statement("UPDATE quotes SET status = 'draft' WHERE status = 'brouillon'");
        DB::statement("UPDATE quotes SET status = 'validated' WHERE status = 'prêt'");
    }
};
