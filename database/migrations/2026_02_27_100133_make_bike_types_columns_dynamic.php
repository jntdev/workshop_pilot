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
        Schema::table('bike_types', function (Blueprint $table) {
            $table->string('category', 50)->change();
            $table->string('size', 10)->nullable()->change();
            $table->string('frame_type', 5)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('bike_types', function (Blueprint $table) {
            $table->string('category', 50)->change();
            $table->string('size', 10)->nullable(false)->change();
            $table->string('frame_type', 5)->nullable(false)->change();
        });
    }
};
