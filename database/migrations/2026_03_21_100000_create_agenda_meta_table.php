<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('agenda_meta', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agenda_version')->default(1);
            $table->timestamps();
        });

        // Insert the initial row
        DB::table('agenda_meta')->insert([
            'id' => 1,
            'agenda_version' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agenda_meta');
    }
};
