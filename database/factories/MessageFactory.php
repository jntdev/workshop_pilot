<?php

namespace Database\Factories;

use App\Enums\WorkMode;
use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Message>
 */
class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        $authorMode = fake()->randomElement(WorkMode::cases());

        return [
            'author_mode' => $authorMode,
            'recipient_mode' => fake()->optional(0.7)->randomElement(WorkMode::cases()),
            'category' => fake()->randomElement(['accueil', 'atelier', 'location', 'autre']),
            'contact_name' => fake()->optional(0.6)->name(),
            'contact_phone' => fake()->optional(0.5)->phoneNumber(),
            'contact_email' => fake()->optional(0.4)->email(),
            'content' => fake()->sentence(10),
            'status' => 'ouvert',
            'read_at' => null,
            'resolved_at' => null,
        ];
    }

    public function fromNicolas(): static
    {
        return $this->state(fn () => [
            'author_mode' => WorkMode::Comptoir,
        ]);
    }

    public function fromJonathan(): static
    {
        return $this->state(fn () => [
            'author_mode' => WorkMode::Atelier,
        ]);
    }

    public function toNicolas(): static
    {
        return $this->state(fn () => [
            'recipient_mode' => WorkMode::Comptoir,
        ]);
    }

    public function toJonathan(): static
    {
        return $this->state(fn () => [
            'recipient_mode' => WorkMode::Atelier,
        ]);
    }

    public function forSelf(): static
    {
        return $this->state(fn () => [
            'recipient_mode' => null,
        ]);
    }

    public function read(): static
    {
        return $this->state(fn () => [
            'read_at' => now()->subMinutes(fake()->numberBetween(5, 120)),
        ]);
    }

    public function resolved(): static
    {
        return $this->state(fn () => [
            'status' => 'resolu',
            'read_at' => now()->subHours(fake()->numberBetween(1, 24)),
            'resolved_at' => now()->subMinutes(fake()->numberBetween(5, 60)),
        ]);
    }

    public function withContact(): static
    {
        return $this->state(fn () => [
            'contact_name' => fake()->name(),
            'contact_phone' => fake()->phoneNumber(),
            'contact_email' => fake()->email(),
        ]);
    }

    public function categoryAccueil(): static
    {
        return $this->state(fn () => [
            'category' => 'accueil',
        ]);
    }

    public function categoryAtelier(): static
    {
        return $this->state(fn () => [
            'category' => 'atelier',
        ]);
    }

    public function categoryLocation(): static
    {
        return $this->state(fn () => [
            'category' => 'location',
        ]);
    }

    public function categoryAutre(): static
    {
        return $this->state(fn () => [
            'category' => 'autre',
        ]);
    }
}
