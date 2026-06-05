<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $sizes = [
            ['name' => 'S/M',  'color' => '#a78bfa', 'sort_order' => 1],
            ['name' => 'M/L',  'color' => '#c084fc', 'sort_order' => 3],
            ['name' => 'L/XL', 'color' => '#e879f9', 'sort_order' => 5],
        ];

        foreach ($sizes as $size) {
            DB::table('bike_sizes')->insertOrIgnore(array_merge($size, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Réordonner S, S/M, M, M/L, L, L/XL
        $order = ['S' => 0, 'S/M' => 1, 'M' => 2, 'M/L' => 3, 'L' => 4, 'L/XL' => 5];
        foreach ($order as $name => $sort) {
            DB::table('bike_sizes')->where('name', $name)->update(['sort_order' => $sort]);
        }
    }

    public function down(): void
    {
        DB::table('bike_sizes')->whereIn('name', ['S/M', 'M/L', 'L/XL'])->delete();
    }
};
