<?php

namespace Database\Seeders;

use App\Models\BikeSize;
use Illuminate\Database\Seeder;

class BikeSizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sizes = [
            ['name' => 'S', 'color' => '#6366f1', 'sort_order' => 0],
            ['name' => 'M', 'color' => '#8b5cf6', 'sort_order' => 1],
            ['name' => 'L', 'color' => '#a855f7', 'sort_order' => 2],
        ];

        foreach ($sizes as $size) {
            BikeSize::updateOrCreate(
                ['name' => $size['name']],
                $size
            );
        }

        $this->command->info(count($sizes).' tailles créées/mises à jour avec succès.');
    }
}
