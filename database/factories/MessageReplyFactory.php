<?php

namespace Database\Factories;

use App\Enums\WorkMode;
use App\Models\Message;
use App\Models\MessageReply;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MessageReply>
 */
class MessageReplyFactory extends Factory
{
    protected $model = MessageReply::class;

    public function definition(): array
    {
        return [
            'message_id' => Message::factory(),
            'author_mode' => fake()->randomElement(WorkMode::cases()),
            'recipient_mode' => fake()->optional(0.7)->randomElement(WorkMode::cases()),
            'content' => fake()->sentence(8),
            'read_at' => null,
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

    public function read(): static
    {
        return $this->state(fn () => [
            'read_at' => now()->subMinutes(fake()->numberBetween(5, 60)),
        ]);
    }
}
