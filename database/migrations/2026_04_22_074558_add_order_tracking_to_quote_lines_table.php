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
            $table->boolean('needs_order')->default(false)->after('estimated_time_minutes');
            $table->timestamp('ordered_at')->nullable()->after('needs_order');
            $table->timestamp('received_at')->nullable()->after('ordered_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quote_lines', function (Blueprint $table) {
            $table->dropColumn(['needs_order', 'ordered_at', 'received_at']);
        });
    }
};
