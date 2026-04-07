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
        Schema::table('messages', function (Blueprint $table) {
            $table->timestamp('last_activity_at')->nullable()->after('resolved_at');
            $table->index('last_activity_at');
        });

        // Initialize last_activity_at with the latest reply date or created_at
        DB::statement('
            UPDATE messages
            SET last_activity_at = COALESCE(
                (SELECT MAX(created_at) FROM message_replies WHERE message_replies.message_id = messages.id),
                messages.created_at
            )
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex(['last_activity_at']);
            $table->dropColumn('last_activity_at');
        });
    }
};
