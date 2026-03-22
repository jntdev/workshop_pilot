<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE messages MODIFY author_mode ENUM('comptoir', 'atelier', 'julien') NOT NULL");
        DB::statement("ALTER TABLE messages MODIFY recipient_mode ENUM('comptoir', 'atelier', 'julien') NULL");
        DB::statement("ALTER TABLE message_replies MODIFY author_mode ENUM('comptoir', 'atelier', 'julien') NOT NULL");
        DB::statement("ALTER TABLE message_replies MODIFY recipient_mode ENUM('comptoir', 'atelier', 'julien') NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE messages MODIFY author_mode ENUM('comptoir', 'atelier') NOT NULL");
        DB::statement("ALTER TABLE messages MODIFY recipient_mode ENUM('comptoir', 'atelier') NULL");
        DB::statement("ALTER TABLE message_replies MODIFY author_mode ENUM('comptoir', 'atelier') NOT NULL");
        DB::statement("ALTER TABLE message_replies MODIFY recipient_mode ENUM('comptoir', 'atelier') NULL");
    }
};
