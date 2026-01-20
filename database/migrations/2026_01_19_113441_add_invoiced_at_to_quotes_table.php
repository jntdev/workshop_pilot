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
        Schema::table('quotes', function (Blueprint $table) {
            $table->timestamp('invoiced_at')->nullable()->after('status');
        });

        // Migrer les devis existants avec statut "facturé" vers invoiced_at
        DB::table('quotes')
            ->where('status', 'facturé')
            ->update(['invoiced_at' => now()]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn('invoiced_at');
        });
    }
};
