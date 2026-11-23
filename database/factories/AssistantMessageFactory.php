<?php

namespace Database\Factories;

use App\Enums\AssistantMessageStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AssistantMessage>
 */
class AssistantMessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'conversation_id' => (string) Str::uuid(),
            'question' => $this->faker->sentence().' ?',
            'status' => AssistantMessageStatus::Pending,
        ];
    }

    public function processing(): static
    {
        return $this->state(fn () => [
            'status' => AssistantMessageStatus::Processing,
            'claimed_at' => now(),
        ]);
    }

    public function answered(string $answer = 'Voici la réponse.'): static
    {
        return $this->state(fn () => [
            'status' => AssistantMessageStatus::Answered,
            'answer' => $answer,
            'claude_session_id' => (string) Str::uuid(),
            'claimed_at' => now()->subSeconds(10),
            'answered_at' => now(),
        ]);
    }

    public function failed(string $error = 'Erreur de test.'): static
    {
        return $this->state(fn () => [
            'status' => AssistantMessageStatus::Failed,
            'error' => $error,
            'claimed_at' => now()->subSeconds(10),
            'answered_at' => now(),
        ]);
    }
}
