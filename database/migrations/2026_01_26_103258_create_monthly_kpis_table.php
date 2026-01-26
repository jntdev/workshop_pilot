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
        Schema::create('monthly_kpis', function (Blueprint $table) {
            $table->id();
            $table->string('metier', 50);
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->unsignedInteger('invoice_count')->default(0);
            $table->decimal('revenue_ht', 12, 2)->default(0);
            $table->decimal('margin_ht', 12, 2)->default(0);
            $table->timestamps();

            $table->unique(['metier', 'year', 'month']);
            $table->index(['metier', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_kpis');
    }
};
