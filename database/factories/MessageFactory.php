<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Message>
 */
class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        return [
            'author_user_id' => User::factory(),
            'recipient_user_id' => null,
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
            'author_user_id' => User::where('email', 'lesvelosdarmorbzh@gmail.com')->first()?->id ?? User::factory(),
        ]);
    }

    public function fromJonathan(): static
    {
        return $this->state(fn () => [
            'author_user_id' => User::where('email', 'jnt.marois@gmail.com')->first()?->id ?? User::factory(),
        ]);
    }

    public function toNicolas(): static
    {
        return $this->state(fn () => [
            'recipient_user_id' => User::where('email', 'lesvelosdarmorbzh@gmail.com')->first()?->id ?? User::factory(),
        ]);
    }

    public function toJonathan(): static
    {
        return $this->state(fn () => [
            'recipient_user_id' => User::where('email', 'jnt.marois@gmail.com')->first()?->id ?? User::factory(),
        ]);
    }

    public function toJulien(): static
    {
        return $this->state(fn () => [
            'recipient_user_id' => User::where('email', 'julien2705@gmail.com')->first()?->id ?? User::factory(),
        ]);
    }

    public function forSelf(): static
    {
        return $this->state(fn () => [
            'recipient_user_id' => null,
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
