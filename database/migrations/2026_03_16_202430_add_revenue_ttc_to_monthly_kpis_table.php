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
        Schema::table('monthly_kpis', function (Blueprint $table) {
            $table->decimal('revenue_ttc', 10, 2)->default(0)->after('revenue_ht');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monthly_kpis', function (Blueprint $table) {
            $table->dropColumn('revenue_ttc');
        });
    }
};
