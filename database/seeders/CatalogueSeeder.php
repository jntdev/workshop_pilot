<?php

namespace Database\Seeders;

use App\Models\ArticleCategory;
use App\Models\ArticleSubcategory;
use Illuminate\Database\Seeder;

class CatalogueSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['name' => 'Pneus', 'subcategories' => ['Chambre à air', 'Pneu route', 'Pneu VTT', 'Pneu ville']],
            ['name' => 'Transmission', 'subcategories' => ['Chaîne 6/7v', 'Chaîne 8v', 'Chaîne 9v', 'Chaîne 10v', 'Chaîne 11v', 'Cassette', 'Roue libre', 'Pédalier', 'Dérailleur']],
            ['name' => 'Freins', 'subcategories' => ['Câble frein', 'Gaine frein', 'Plaquette frein', 'Disque de frein', 'Levier de frein']],
            ['name' => 'Direction', 'subcategories' => ['Câble vitesse', 'Gaine vitesse', 'Poignée tournante', 'Poignée grip']],
            ['name' => 'Roues', 'subcategories' => ['Rayons', 'Jante', 'Moyeu', 'Valve']],
            ['name' => 'Électrique (VAE)', 'subcategories' => ['Batterie', 'Moteur', 'Capteur', 'Câble électrique', 'Chargeur']],
            ['name' => 'Éclairage', 'subcategories' => ['Éclairage avant', 'Éclairage arrière', 'Pile / batterie éclairage']],
            ['name' => 'Accessoires', 'subcategories' => ['Antivol', 'Béquille', 'Panier / sacoche', 'Selle', 'Tige de selle', 'Guidon']],
            ['name' => 'Consommables', 'subcategories' => ['Lubrifiant', 'Dégraissant', 'Graisse', 'Colle', 'Rustines']],
        ];

        foreach ($data as $i => $categoryData) {
            $category = ArticleCategory::firstOrCreate(
                ['name' => $categoryData['name']],
                ['sort_order' => $i]
            );

            foreach ($categoryData['subcategories'] as $j => $subcategoryName) {
                ArticleSubcategory::firstOrCreate(
                    ['article_category_id' => $category->id, 'name' => $subcategoryName],
                    ['sort_order' => $j]
                );
            }
        }
    }
}
