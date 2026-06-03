<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->timestamp('paid_at')->nullable()->after('invoiced_at');
        });

        // Pour les factures existantes, paid_at = invoiced_at
        DB::statement('UPDATE quotes SET paid_at = invoiced_at WHERE invoiced_at IS NOT NULL');
    }

    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn('paid_at');
        });
    }
};
