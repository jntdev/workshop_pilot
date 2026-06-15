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
        Schema::create('article_subcategories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_category_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['article_category_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_subcategories');
    }
};
