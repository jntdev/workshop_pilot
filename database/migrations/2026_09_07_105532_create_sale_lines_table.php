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
        Schema::create('sale_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('article_id')->nullable()->nullOnDelete()->constrained();
            $table->string('designation', 255);
            $table->string('reference', 100)->nullable();
            $table->decimal('quantity', 10, 2)->default(1);
            $table->integer('purchase_price_ht')->default(0);
            $table->integer('unit_price_ttc')->default(0);
            $table->decimal('tva_rate', 5, 2)->default(20.00);
            $table->integer('line_total_ttc')->default(0);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->unique(['sale_id', 'article_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_lines');
    }
};
