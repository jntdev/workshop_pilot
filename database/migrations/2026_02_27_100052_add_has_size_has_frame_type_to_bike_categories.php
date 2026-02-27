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
        Schema::table('bike_categories', function (Blueprint $table) {
            $table->boolean('has_size')->default(true)->after('has_battery');
            $table->boolean('has_frame_type')->default(true)->after('has_size');
        });
    }

    public function down(): void
    {
        Schema::table('bike_categories', function (Blueprint $table) {
            $table->dropColumn(['has_size', 'has_frame_type']);
        });
    }
};
