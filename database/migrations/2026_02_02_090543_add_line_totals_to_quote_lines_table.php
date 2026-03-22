<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Ajoute les colonnes de totaux par ligne :
     * - line_purchase_ht : coût d'achat total (PA unitaire × quantité)
     * - line_margin_ht : marge totale de la ligne
     * - line_total_ht : montant facturé HT (PV unitaire × quantité)
     * - line_total_ttc : montant facturé TTC
     */
    public function up(): void
    {
        Schema::table('quote_lines', function (Blueprint $table) {
            $table->decimal('line_purchase_ht', 10, 2)->nullable()->after('margin_rate');
            $table->decimal('line_margin_ht', 10, 2)->nullable()->after('line_purchase_ht');
            $table->decimal('line_total_ht', 10, 2)->nullable()->after('line_margin_ht');
            $table->decimal('line_total_ttc', 10, 2)->nullable()->after('line_total_ht');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quote_lines', function (Blueprint $table) {
            $table->dropColumn(['line_purchase_ht', 'line_margin_ht', 'line_total_ht', 'line_total_ttc']);
        });
    }
};
