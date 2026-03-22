<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // MySQL: Modifier l'enum pour ajouter 'rail'
        DB::statement("ALTER TABLE bikes MODIFY COLUMN battery_type ENUM('rack', 'gourde', 'rail') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revenir à l'enum sans 'rail' (attention: perte de données si 'rail' est utilisé)
        DB::statement("UPDATE bikes SET battery_type = 'rack' WHERE battery_type = 'rail'");
        DB::statement("ALTER TABLE bikes MODIFY COLUMN battery_type ENUM('rack', 'gourde') NULL");
    }
};
