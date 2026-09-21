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
        Schema::table('articles', function (Blueprint $table) {
            $table->foreignId('lot_article_id')->nullable()->after('supplier_id')->constrained('articles')->nullOnDelete();
            $table->unsignedInteger('lot_quantity')->nullable()->after('lot_article_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropForeign(['lot_article_id']);
            $table->dropColumn(['lot_article_id', 'lot_quantity']);
        });
    }
};
