<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        // Convertir les anciennes valeurs vers les nouvelles
        DB::table('quotes')->where('status', 'draft')->update(['status' => 'brouillon']);
        DB::table('quotes')->where('status', 'validated')->update(['status' => 'prêt']);

        if ($driver !== 'sqlite') {
            // Modifier l'enum pour inclure tous les nouveaux statuts (MySQL uniquement)
            DB::statement("ALTER TABLE quotes MODIFY COLUMN status ENUM('brouillon', 'prêt', 'modifiable', 'facturé') DEFAULT 'brouillon'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        // Reconvertir vers les anciennes valeurs
        DB::table('quotes')->where('status', 'brouillon')->update(['status' => 'draft']);
        DB::table('quotes')->where('status', 'prêt')->update(['status' => 'validated']);

        if ($driver !== 'sqlite') {
            // Remettre l'ancien enum (MySQL uniquement)
            DB::statement("ALTER TABLE quotes MODIFY COLUMN status ENUM('draft', 'validated') DEFAULT 'draft'");
        }
    }
};
