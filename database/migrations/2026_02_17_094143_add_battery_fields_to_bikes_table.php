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
        Schema::table('bikes', function (Blueprint $table) {
            // Modèle du vélo (500, 625, etc.) - pour tous les vélos
            $table->enum('model', ['500', '625', 'autre'])->nullable()->after('frame_type');
            // Type de batterie - uniquement pour les VAE
            $table->enum('battery_type', ['rack', 'gourde'])->nullable()->after('model');
        });

        // Valeurs par défaut pour les vélos existants
        \DB::table('bikes')->update(['model' => '500']);
        \DB::table('bikes')->where('category', 'VAE')->update(['battery_type' => 'rack']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bikes', function (Blueprint $table) {
            $table->dropColumn(['model', 'battery_type']);
        });
    }
};
