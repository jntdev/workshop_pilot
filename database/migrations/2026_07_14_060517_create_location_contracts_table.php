<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('location_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();
            $table->uuid('token')->unique();
            $table->json('accessories');
            $table->integer('caution_amount');
            $table->string('return_time_text');
            $table->string('operator_name');
            $table->json('contract_data');
            $table->timestamp('signed_at')->nullable();
            $table->text('signature_image')->nullable();
            $table->string('signer_name')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamp('expires_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('location_contracts');
    }
};
