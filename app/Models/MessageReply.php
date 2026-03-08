<?php

namespace App\Models;

use App\Enums\WorkMode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id',
        'author_mode',
        'recipient_mode',
        'content',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'author_mode' => WorkMode::class,
            'recipient_mode' => WorkMode::class,
            'read_at' => 'datetime',
        ];
    }

    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }

    public function markAsRead(): void
    {
        if ($this->read_at === null) {
            $this->update(['read_at' => now()]);
        }
    }

    public function authorLabel(): string
    {
        return $this->author_mode->label();
    }

    public function recipientLabel(): ?string
    {
        return $this->recipient_mode?->label();
    }
}
