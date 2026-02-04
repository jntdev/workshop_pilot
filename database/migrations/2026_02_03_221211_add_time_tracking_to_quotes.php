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
        Schema::table('quote_lines', function (Blueprint $table) {
            $table->unsignedInteger('estimated_time_minutes')->nullable()->after('position');
        });

        Schema::table('quotes', function (Blueprint $table) {
            $table->unsignedInteger('total_estimated_time_minutes')->nullable()->after('margin_total_ht');
            $table->unsignedInteger('actual_time_minutes')->nullable()->after('total_estimated_time_minutes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quote_lines', function (Blueprint $table) {
            $table->dropColumn('estimated_time_minutes');
        });

        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn(['total_estimated_time_minutes', 'actual_time_minutes']);
        });
    }
};
