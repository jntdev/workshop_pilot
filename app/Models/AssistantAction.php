<?php

namespace App\Models;

use App\Enums\AssistantActionStatus;
use App\Enums\AssistantActionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Une action proposée par l'assistant. Elle ne change rien tant qu'elle n'est pas confirmée
 * par l'utilisateur qui a posé la question ; ensuite elle garde l'état avant et après.
 */
class AssistantAction extends Model
{
    protected $attributes = [
        'status' => 'proposed',
    ];

    protected $fillable = [
        'assistant_message_id',
        'user_id',
        'conversation_id',
        'type',
        'subject_type',
        'subject_id',
        'payload',
        'before',
        'after',
        'summary',
        'warnings',
        'status',
        'error',
        'expires_at',
        'confirmed_at',
        'confirmed_by',
        'undone_at',
    ];

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'type' => AssistantActionType::class,
            'status' => AssistantActionStatus::class,
            'payload' => 'array',
            'before' => 'array',
            'after' => 'array',
            'warnings' => 'array',
            'expires_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'undone_at' => 'datetime',
        ];
    }

    public function message(): BelongsTo
    {
        return $this->belongsTo(AssistantMessage::class, 'assistant_message_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function confirmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    /**
     * Marque comme expirées les propositions restées sans réponse trop longtemps.
     */
    public static function expireStale(): int
    {
        return static::query()
            ->where('status', AssistantActionStatus::Proposed->value)
            ->where('expires_at', '<', now())
            ->update(['status' => AssistantActionStatus::Expired->value]);
    }
}
