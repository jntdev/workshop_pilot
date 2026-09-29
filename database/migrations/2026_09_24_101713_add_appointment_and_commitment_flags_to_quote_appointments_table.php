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
        Schema::table('quote_appointments', function (Blueprint $table) {
            $table->boolean('is_appointment')->default(false)->after('completed');
            $table->boolean('is_commitment')->default(false)->after('is_appointment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quote_appointments', function (Blueprint $table) {
            $table->dropColumn(['is_appointment', 'is_commitment']);
        });
    }
};
