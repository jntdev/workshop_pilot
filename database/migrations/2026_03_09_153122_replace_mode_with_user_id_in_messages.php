<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ajouter les nouvelles colonnes (nullable initialement pour la migration des données)
        Schema::table('messages', function (Blueprint $table) {
            $table->unsignedBigInteger('author_user_id')->nullable()->after('id');
            $table->unsignedBigInteger('recipient_user_id')->nullable()->after('author_user_id');
        });

        Schema::table('message_replies', function (Blueprint $table) {
            $table->unsignedBigInteger('author_user_id')->nullable()->after('message_id');
            $table->unsignedBigInteger('recipient_user_id')->nullable()->after('author_user_id');
        });

        // 2. Migrer les données : mode → user_id
        $users = DB::table('users')->whereNotNull('work_mode')->get()->keyBy('work_mode');

        DB::table('messages')->get()->each(function ($row) use ($users) {
            DB::table('messages')->where('id', $row->id)->update([
                'author_user_id' => $users->get($row->author_mode)?->id,
                'recipient_user_id' => $row->recipient_mode ? $users->get($row->recipient_mode)?->id : null,
            ]);
        });

        DB::table('message_replies')->get()->each(function ($row) use ($users) {
            DB::table('message_replies')->where('id', $row->id)->update([
                'author_user_id' => $users->get($row->author_mode)?->id,
                'recipient_user_id' => $row->recipient_mode ? $users->get($row->recipient_mode)?->id : null,
            ]);
        });

        // 3. Supprimer les anciens index et colonnes mode
        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex(['status', 'recipient_mode']);
            $table->dropIndex(['status', 'author_mode']);
            $table->dropColumn(['author_mode', 'recipient_mode']);
        });

        Schema::table('message_replies', function (Blueprint $table) {
            $table->dropColumn(['author_mode', 'recipient_mode']);
        });

        // 4. Ajouter les FK et index sur les nouvelles colonnes
        Schema::table('messages', function (Blueprint $table) {
            $table->foreign('author_user_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('recipient_user_id')->references('id')->on('users')->nullOnDelete();
            $table->index(['status', 'recipient_user_id']);
            $table->index(['status', 'author_user_id']);
        });

        Schema::table('message_replies', function (Blueprint $table) {
            $table->foreign('author_user_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('recipient_user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['author_user_id']);
            $table->dropForeign(['recipient_user_id']);
            $table->dropIndex(['status', 'recipient_user_id']);
            $table->dropIndex(['status', 'author_user_id']);
            $table->dropColumn(['author_user_id', 'recipient_user_id']);
        });

        Schema::table('message_replies', function (Blueprint $table) {
            $table->dropForeign(['author_user_id']);
            $table->dropForeign(['recipient_user_id']);
            $table->dropColumn(['author_user_id', 'recipient_user_id']);
        });
    }
};
