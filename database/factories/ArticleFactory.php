<?php

namespace Database\Factories;

use App\Models\ArticleCategory;
use App\Models\ArticleSubcategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Sous-catégorie manuelle par défaut, pour que l'article créé soit éditable
            // (comportement historique attendu par les tests existants) — les tests
            // voulant explicitement un article catalogue passent leur propre subcategory.
            'article_subcategory_id' => ArticleSubcategory::factory()->for(ArticleCategory::factory()->manual(), 'category'),
            'reference' => $this->faker->unique()->bothify('??-###'),
            'designation' => $this->faker->sentence(3),
            'purchase_price_ht' => $this->faker->numberBetween(100, 5000),
            'sale_price_ttc' => $this->faker->numberBetween(200, 8000),
            'tva_rate' => 20.00,
            'unit' => $this->faker->randomElement(['pièce', 'paire', 'kit', 'litre']),
            'notes' => $this->faker->optional()->sentence(),
            'sort_order' => 0,
            'barcode' => $this->faker->optional()->ean13(),
            'image_url' => $this->faker->optional()->imageUrl(),
            'weight_kg' => $this->faker->optional()->randomFloat(3, 0, 5),
        ];
    }
}
