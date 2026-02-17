<?php

namespace Database\Seeders;

use App\Models\Bike;
use App\Models\BikeCategory;
use App\Models\BikeSize;
use Illuminate\Database\Seeder;

class BikeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les IDs des catégories et tailles
        $categoryIds = BikeCategory::pluck('id', 'name')->toArray();
        $sizeIds = BikeSize::pluck('id', 'name')->toArray();

        $bikes = [
            // VAE Taille S - cadre bas
            ['category' => 'VAE', 'size' => 'S', 'frame_type' => 'b', 'model' => '500', 'battery_type' => 'rack', 'name' => 'ESb1', 'status' => 'OK'],
            ['category' => 'VAE', 'size' => 'S', 'frame_type' => 'b', 'model' => '500', 'battery_type' => 'rack', 'name' => 'ESb2', 'status' => 'OK'],
            ['category' => 'VAE', 'size' => 'S', 'frame_type' => 'b', 'model' => '625', 'battery_type' => 'gourde', 'name' => 'ESb3', 'status' => 'OK'],
            // VAE Taille S - cadre haut
            ['category' => 'VAE', 'size' => 'S', 'frame_type' => 'h', 'model' => '500', 'battery_type' => 'rail', 'name' => 'ESh1', 'status' => 'OK'],
            ['category' => 'VAE', 'size' => 'S', 'frame_type' => 'h', 'model' => '625', 'battery_type' => 'gourde', 'name' => 'ESh2', 'status' => 'OK'],

            // VAE Taille M - cadre bas
            ['category' => 'VAE', 'size' => 'M', 'frame_type' => 'b', 'model' => '500', 'battery_type' => 'rack', 'name' => 'EMb1', 'status' => 'OK'],
            ['category' => 'VAE', 'size' => 'M', 'frame_type' => 'b', 'model' => '500', 'battery_type' => 'rack', 'name' => 'EMb2', 'status' => 'OK'],
            ['category' => 'VAE', 'size' => 'M', 'frame_type' => 'b', 'model' => '625', 'battery_type' => 'gourde', 'name' => 'EMb3', 'status' => 'OK'],
            ['category' => 'VAE', 'size' => 'M', 'frame_type' => 'b', 'model' => '625', 'battery_type' => 'rail', 'name' => 'EMb4', 'status' => 'OK'],
            // VAE Taille M - cadre haut
            ['category' => 'VAE', 'size' => 'M', 'frame_type' => 'h', 'model' => '500', 'battery_type' => 'rack', 'name' => 'EMh1', 'status' => 'OK'],
            ['category' => 'VAE', 'size' => 'M', 'frame_type' => 'h', 'model' => '625', 'battery_type' => 'gourde', 'name' => 'EMh2', 'status' => 'OK'],
            ['category' => 'VAE', 'size' => 'M', 'frame_type' => 'h', 'model' => '625', 'battery_type' => 'rail', 'name' => 'EMh3', 'status' => 'OK'],

            // VAE Taille L - cadre bas
            ['category' => 'VAE', 'size' => 'L', 'frame_type' => 'b', 'model' => '500', 'battery_type' => 'rack', 'name' => 'ELb1', 'status' => 'OK'],
            ['category' => 'VAE', 'size' => 'L', 'frame_type' => 'b', 'model' => '625', 'battery_type' => 'gourde', 'name' => 'ELb2', 'status' => 'OK'],
            ['category' => 'VAE', 'size' => 'L', 'frame_type' => 'b', 'model' => '625', 'battery_type' => 'rail', 'name' => 'ELb3', 'status' => 'HS', 'notes' => 'Batterie HS'],
            // VAE Taille L - cadre haut
            ['category' => 'VAE', 'size' => 'L', 'frame_type' => 'h', 'model' => '500', 'battery_type' => 'rack', 'name' => 'ELh1', 'status' => 'OK'],
            ['category' => 'VAE', 'size' => 'L', 'frame_type' => 'h', 'model' => '625', 'battery_type' => 'gourde', 'name' => 'ELh2', 'status' => 'OK'],
            ['category' => 'VAE', 'size' => 'L', 'frame_type' => 'h', 'model' => '625', 'battery_type' => 'rail', 'name' => 'ELh3', 'status' => 'OK'],
            ['category' => 'VAE', 'size' => 'L', 'frame_type' => 'h', 'model' => '500', 'battery_type' => 'rail', 'name' => 'ELh4', 'status' => 'OK'],

            // VTC Taille S - cadre bas
            ['category' => 'VTC', 'size' => 'S', 'frame_type' => 'b', 'model' => '500', 'name' => 'Sb1', 'status' => 'OK'],
            ['category' => 'VTC', 'size' => 'S', 'frame_type' => 'b', 'model' => '500', 'name' => 'Sb2', 'status' => 'OK'],
            // VTC Taille S - cadre haut
            ['category' => 'VTC', 'size' => 'S', 'frame_type' => 'h', 'model' => '500', 'name' => 'Sh1', 'status' => 'OK'],
            ['category' => 'VTC', 'size' => 'S', 'frame_type' => 'h', 'model' => '625', 'name' => 'Sh2', 'status' => 'OK'],

            // VTC Taille M - cadre bas
            ['category' => 'VTC', 'size' => 'M', 'frame_type' => 'b', 'model' => '500', 'name' => 'Mb1', 'status' => 'OK'],
            ['category' => 'VTC', 'size' => 'M', 'frame_type' => 'b', 'model' => '500', 'name' => 'Mb2', 'status' => 'OK'],
            ['category' => 'VTC', 'size' => 'M', 'frame_type' => 'b', 'model' => '625', 'name' => 'Mb3', 'status' => 'OK'],
            // VTC Taille M - cadre haut
            ['category' => 'VTC', 'size' => 'M', 'frame_type' => 'h', 'model' => '500', 'name' => 'Mh1', 'status' => 'OK'],
            ['category' => 'VTC', 'size' => 'M', 'frame_type' => 'h', 'model' => '625', 'name' => 'Mh2', 'status' => 'OK'],
            ['category' => 'VTC', 'size' => 'M', 'frame_type' => 'h', 'model' => '500', 'name' => 'Mh3', 'status' => 'HS', 'notes' => 'Roue voilee'],

            // VTC Taille L - cadre bas
            ['category' => 'VTC', 'size' => 'L', 'frame_type' => 'b', 'model' => '500', 'name' => 'Lb1', 'status' => 'OK'],
            ['category' => 'VTC', 'size' => 'L', 'frame_type' => 'b', 'model' => '625', 'name' => 'Lb2', 'status' => 'OK'],
            // VTC Taille L - cadre haut
            ['category' => 'VTC', 'size' => 'L', 'frame_type' => 'h', 'model' => '500', 'name' => 'Lh1', 'status' => 'OK'],
            ['category' => 'VTC', 'size' => 'L', 'frame_type' => 'h', 'model' => '625', 'name' => 'Lh2', 'status' => 'OK'],
            ['category' => 'VTC', 'size' => 'L', 'frame_type' => 'h', 'model' => '500', 'name' => 'Lh3', 'status' => 'OK'],
        ];

        $sortOrder = 0;
        foreach ($bikes as $bikeData) {
            Bike::updateOrCreate(
                ['name' => $bikeData['name']],
                [
                    'bike_category_id' => $categoryIds[$bikeData['category']],
                    'bike_size_id' => $sizeIds[$bikeData['size']],
                    'frame_type' => $bikeData['frame_type'],
                    'model' => $bikeData['model'] ?? null,
                    'battery_type' => $bikeData['battery_type'] ?? null,
                    'status' => $bikeData['status'],
                    'notes' => $bikeData['notes'] ?? null,
                    'sort_order' => $sortOrder++,
                ]
            );
        }

        $this->command->info(count($bikes).' vélos créés/mis à jour avec succès.');
    }
}
