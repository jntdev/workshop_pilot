<?php

namespace Database\Seeders;

use App\Models\BikeCategory;
use Illuminate\Database\Seeder;

class BikeCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'VAE', 'color' => '#FFD233', 'has_battery' => true, 'sort_order' => 0],
            ['name' => 'VTC', 'color' => '#005D66', 'has_battery' => false, 'sort_order' => 1],
        ];

        foreach ($categories as $category) {
            BikeCategory::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }

        $this->command->info(count($categories).' catégories créées/mises à jour avec succès.');
    }
}
