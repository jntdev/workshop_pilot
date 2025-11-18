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
        Schema::create('quote_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('reference')->nullable();
            $table->decimal('purchase_price_ht', 10, 2)->default(0);
            $table->decimal('sale_price_ht', 10, 2)->default(0);
            $table->decimal('sale_price_ttc', 10, 2)->default(0);
            $table->decimal('margin_amount_ht', 10, 2)->default(0);
            $table->decimal('margin_rate', 7, 4)->default(0);
            $table->decimal('tva_rate', 7, 4)->default(20.0000);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_lines');
    }
};
