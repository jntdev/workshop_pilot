<?php

namespace Database\Factories;

use App\Enums\ArticleCategorySource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ArticleCategory>
 */
class ArticleCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'sort_order' => $this->faker->numberBetween(0, 10),
            'source' => ArticleCategorySource::Catalogue,
        ];
    }

    public function manual(): static
    {
        return $this->state(['source' => ArticleCategorySource::Manual]);
    }
}
