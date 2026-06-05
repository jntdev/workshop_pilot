<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $maxOrder = DB::table('bike_categories')->max('sort_order') ?? 0;

        DB::table('bike_categories')->insertOrIgnore([
            'name' => 'Cargo',
            'color' => '#10b981',
            'has_battery' => true,
            'has_size' => false,
            'has_frame_type' => false,
            'sort_order' => $maxOrder + 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('bike_categories')->where('name', 'Cargo')->delete();
    }
};
