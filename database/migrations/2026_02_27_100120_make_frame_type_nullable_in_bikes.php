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
        Schema::table('bikes', function (Blueprint $table) {
            $table->string('frame_type', 5)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('bikes', function (Blueprint $table) {
            $table->string('frame_type', 5)->nullable(false)->default('b')->change();
        });
    }
};
