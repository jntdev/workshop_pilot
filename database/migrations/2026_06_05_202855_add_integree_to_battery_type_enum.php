<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE bikes MODIFY COLUMN battery_type ENUM('rack', 'gourde', 'rail', 'intégrée') NULL");
    }

    public function down(): void
    {
        DB::statement("UPDATE bikes SET battery_type = 'rack' WHERE battery_type = 'intégrée'");
        DB::statement("ALTER TABLE bikes MODIFY COLUMN battery_type ENUM('rack', 'gourde', 'rail') NULL");
    }
};
