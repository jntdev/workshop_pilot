<?php

namespace Database\Seeders;

use App\Models\Bike;
use Illuminate\Database\Seeder;

class BikeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Supprimer les vélos existants
        Bike::truncate();

        $bikes = [
            // VAE Taille S
            ['category' => 'VAE', 'size' => 'S', 'frame_type' => 'b', 'name' => 'ES1', 'status' => 'OK'],
            ['category' => 'VAE', 'size' => 'S', 'frame_type' => 'b', 'name' => 'ES2', 'status' => 'OK'],
            ['category' => 'VAE', 'size' => 'S', 'frame_type' => 'h', 'name' => 'ES3', 'status' => 'OK'],
            ['category' => 'VAE', 'size' => 'S', 'frame_type' => 'h', 'name' => 'ES4', 'status' => 'HS', 'notes' => 'Batterie HS - en attente remplacement'],

            // VAE Taille M
            ['category' => 'VAE', 'size' => 'M', 'frame_type' => 'b', 'name' => 'EM1', 'status' => 'OK'],
            ['category' => 'VAE', 'size' => 'M', 'frame_type' => 'b', 'name' => 'EM2', 'status' => 'OK'],
            ['category' => 'VAE', 'size' => 'M', 'frame_type' => 'b', 'name' => 'EM3', 'status' => 'OK'],
            ['category' => 'VAE', 'size' => 'M', 'frame_type' => 'h', 'name' => 'EM4', 'status' => 'OK'],
            ['category' => 'VAE', 'size' => 'M', 'frame_type' => 'h', 'name' => 'EM5', 'status' => 'OK'],
            ['category' => 'VAE', 'size' => 'M', 'frame_type' => 'h', 'name' => 'EM6', 'status' => 'OK'],

            // VAE Taille L
            ['category' => 'VAE', 'size' => 'L', 'frame_type' => 'b', 'name' => 'EL1', 'status' => 'OK'],
            ['category' => 'VAE', 'size' => 'L', 'frame_type' => 'b', 'name' => 'EL2', 'status' => 'OK'],
            ['category' => 'VAE', 'size' => 'L', 'frame_type' => 'h', 'name' => 'EL3', 'status' => 'OK'],
            ['category' => 'VAE', 'size' => 'L', 'frame_type' => 'h', 'name' => 'EL4', 'status' => 'OK'],
            ['category' => 'VAE', 'size' => 'L', 'frame_type' => 'h', 'name' => 'EL5', 'status' => 'HS', 'notes' => 'Moteur en panne'],
            ['category' => 'VAE', 'size' => 'L', 'frame_type' => 'h', 'name' => 'EL6', 'status' => 'OK'],

            // VAE Taille XL
            ['category' => 'VAE', 'size' => 'XL', 'frame_type' => 'h', 'name' => 'EXL1', 'status' => 'OK'],
            ['category' => 'VAE', 'size' => 'XL', 'frame_type' => 'h', 'name' => 'EXL2', 'status' => 'OK'],

            // VTC Taille S
            ['category' => 'VTC', 'size' => 'S', 'frame_type' => 'b', 'name' => 'S1', 'status' => 'OK'],
            ['category' => 'VTC', 'size' => 'S', 'frame_type' => 'b', 'name' => 'S2', 'status' => 'OK'],
            ['category' => 'VTC', 'size' => 'S', 'frame_type' => 'h', 'name' => 'S3', 'status' => 'OK'],

            // VTC Taille M
            ['category' => 'VTC', 'size' => 'M', 'frame_type' => 'b', 'name' => 'M1', 'status' => 'OK'],
            ['category' => 'VTC', 'size' => 'M', 'frame_type' => 'b', 'name' => 'M2', 'status' => 'OK'],
            ['category' => 'VTC', 'size' => 'M', 'frame_type' => 'b', 'name' => 'M3', 'status' => 'OK'],
            ['category' => 'VTC', 'size' => 'M', 'frame_type' => 'h', 'name' => 'M4', 'status' => 'OK'],
            ['category' => 'VTC', 'size' => 'M', 'frame_type' => 'h', 'name' => 'M5', 'status' => 'OK'],
            ['category' => 'VTC', 'size' => 'M', 'frame_type' => 'h', 'name' => 'M6', 'status' => 'HS', 'notes' => 'Roue voilée'],

            // VTC Taille L
            ['category' => 'VTC', 'size' => 'L', 'frame_type' => 'b', 'name' => 'L1', 'status' => 'OK'],
            ['category' => 'VTC', 'size' => 'L', 'frame_type' => 'b', 'name' => 'L2', 'status' => 'OK'],
            ['category' => 'VTC', 'size' => 'L', 'frame_type' => 'h', 'name' => 'L3', 'status' => 'OK'],
            ['category' => 'VTC', 'size' => 'L', 'frame_type' => 'h', 'name' => 'L4', 'status' => 'OK'],

            // VTC Taille XL
            ['category' => 'VTC', 'size' => 'XL', 'frame_type' => 'h', 'name' => 'XL1', 'status' => 'OK'],
            ['category' => 'VTC', 'size' => 'XL', 'frame_type' => 'h', 'name' => 'XL2', 'status' => 'OK'],
            ['category' => 'VTC', 'size' => 'XL', 'frame_type' => 'h', 'name' => 'XL3', 'status' => 'OK'],
        ];

        $sortOrder = 0;
        foreach ($bikes as $bikeData) {
            Bike::create([
                'category' => $bikeData['category'],
                'size' => $bikeData['size'],
                'frame_type' => $bikeData['frame_type'],
                'name' => $bikeData['name'],
                'status' => $bikeData['status'],
                'notes' => $bikeData['notes'] ?? null,
                'sort_order' => $sortOrder++,
            ]);
        }

        $this->command->info(count($bikes).' vélos créés avec succès.');
    }
}
