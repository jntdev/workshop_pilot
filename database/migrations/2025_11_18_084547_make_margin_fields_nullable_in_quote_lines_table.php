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
        Schema::table('quote_lines', function (Blueprint $table) {
            $table->decimal('margin_amount_ht', 10, 2)->nullable()->change();
            $table->decimal('margin_rate', 7, 4)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quote_lines', function (Blueprint $table) {
            $table->decimal('margin_amount_ht', 10, 2)->default(0)->change();
            $table->decimal('margin_rate', 7, 4)->default(0)->change();
        });
    }
};
