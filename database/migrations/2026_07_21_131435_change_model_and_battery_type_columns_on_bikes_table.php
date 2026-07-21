<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // model was ENUM('500','625','autre') — convert to free-text VARCHAR
        DB::statement('ALTER TABLE bikes MODIFY model VARCHAR(50) NULL');

        // battery_type was ENUM('rack','gourde') — add missing values
        DB::statement("ALTER TABLE bikes MODIFY battery_type ENUM('rack','gourde','rail','intégrée') NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE bikes MODIFY battery_type ENUM('rack','gourde') NULL");
        DB::statement("ALTER TABLE bikes MODIFY model ENUM('500','625','autre') NULL");
    }
};
