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
        Schema::table('demandes_partenaire', function (Blueprint $table) {
            $table->string('creneau')->default('journee')->after('date_fin');
        });
    }

    public function down(): void
    {
        Schema::table('demandes_partenaire', function (Blueprint $table) {
            $table->dropColumn('creneau');
        });
    }
};
