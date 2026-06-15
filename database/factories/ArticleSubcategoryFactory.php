<?php

namespace Database\Factories;

use App\Models\ArticleCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ArticleSubcategory>
 */
class ArticleSubcategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'article_category_id' => ArticleCategory::factory(),
            'name' => $this->faker->word(),
            'sort_order' => $this->faker->numberBetween(0, 10),
        ];
    }
}
