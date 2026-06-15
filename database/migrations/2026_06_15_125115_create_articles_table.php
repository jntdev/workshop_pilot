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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_subcategory_id')->nullable()->nullOnDelete()->constrained();
            $table->string('reference', 100)->unique();
            $table->string('designation', 255);
            $table->integer('purchase_price_ht')->default(0);
            $table->integer('sale_price_ht')->default(0);
            $table->decimal('tva_rate', 5, 2)->default(20.00);
            $table->string('unit', 50)->default('pièce');
            $table->string('supplier', 255)->nullable();
            $table->text('notes')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
