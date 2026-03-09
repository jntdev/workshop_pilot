<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\MessageReply;
use App\Models\User;
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
            'author_user_id' => User::factory(),
            'recipient_user_id' => null,
            'content' => fake()->sentence(8),
            'read_at' => null,
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

    public function read(): static
    {
        return $this->state(fn () => [
            'read_at' => now()->subMinutes(fake()->numberBetween(5, 60)),
        ]);
    }
}
