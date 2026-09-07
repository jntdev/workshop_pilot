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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->nullable()->unique();
            $table->foreignId('client_id')->nullable()->nullOnDelete()->constrained();
            $table->foreignId('user_id')->nullable()->nullOnDelete()->constrained();
            $table->string('status', 20)->default('draft');
            $table->string('payment_method', 20)->nullable();
            $table->integer('total_ht')->default(0);
            $table->integer('total_tva')->default(0);
            $table->integer('total_ttc')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
