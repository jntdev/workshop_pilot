<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Élargir la colonne en string pour accepter toutes les valeurs transitoires.
        Schema::table('quotes', function (Blueprint $table) {
            $table->string('status')->default('reception')->change();
        });

        DB::table('quotes')
            ->whereNotNull('invoiced_at')
            ->whereNotIn('status', ['invoiced'])
            ->update(['status' => 'invoiced']);

        DB::table('quotes')
            ->whereNull('invoiced_at')
            ->whereNotIn('status', ['reception', 'to_complete', 'to_quote', 'pending_validation', 'validated', 'in_progress', 'done'])
            ->update(['status' => 'reception']);
    }

    public function down(): void
    {
        DB::table('quotes')
            ->whereNull('invoiced_at')
            ->update(['status' => 'brouillon']);

        Schema::table('quotes', function (Blueprint $table) {
            $table->enum('status', ['brouillon', 'prêt', 'modifiable', 'facturé'])->default('brouillon')->change();
        });
    }
};
