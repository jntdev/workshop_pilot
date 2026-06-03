<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bike_maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bike_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('description');
            $table->unsignedInteger('cost')->nullable()->comment('en centimes');
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->enum('status', ['todo', 'done'])->default('todo');
            $table->timestamps();

            $table->index(['bike_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bike_maintenance_logs');
    }
};
