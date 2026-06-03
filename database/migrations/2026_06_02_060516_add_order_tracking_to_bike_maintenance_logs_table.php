<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bike_maintenance_logs', function (Blueprint $table) {
            $table->boolean('needs_order')->default(false)->after('status');
            $table->string('reference')->nullable()->after('needs_order');
            $table->timestamp('ordered_at')->nullable()->after('reference');
            $table->timestamp('received_at')->nullable()->after('ordered_at');
        });
    }

    public function down(): void
    {
        Schema::table('bike_maintenance_logs', function (Blueprint $table) {
            $table->dropColumn(['needs_order', 'reference', 'ordered_at', 'received_at']);
        });
    }
};
