<?php

namespace App\Models;

use App\Enums\AssistantMessageStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Une question posée par un utilisateur à l'assistant de l'atelier, avec sa réponse.
 * Sert aussi de journal : toutes les questions et réponses restent consultables.
 */
class AssistantMessage extends Model
{
    /** @use HasFactory<\Database\Factories\AssistantMessageFactory> */
    use HasFactory;

    protected $attributes = [
        'status' => 'pending',
    ];

    protected $fillable = [
        'user_id',
        'conversation_id',
        'question',
        'status',
        'answer',
        'error',
        'claude_session_id',
        'consultations',
        'claimed_at',
        'answered_at',
    ];

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'status' => AssistantMessageStatus::class,
            'consultations' => 'array',
            'claimed_at' => 'datetime',
            'answered_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function actions(): HasMany
    {
        return $this->hasMany(AssistantAction::class)->orderBy('id');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', AssistantMessageStatus::Pending->value);
    }

    public function scopeUnfinished(Builder $query): Builder
    {
        return $query->whereIn('status', [
            AssistantMessageStatus::Pending->value,
            AssistantMessageStatus::Processing->value,
        ]);
    }

    /**
     * Marque en échec les questions prises par le Mac mais restées sans réponse trop longtemps.
     */
    public static function failStale(): int
    {
        return static::query()
            ->where('status', AssistantMessageStatus::Processing->value)
            ->where('claimed_at', '<', now()->subSeconds((int) config('assistant.processing_timeout_seconds')))
            ->update([
                'status' => AssistantMessageStatus::Failed->value,
                'error' => 'Délai dépassé : l\'assistant n\'a pas répondu.',
                'answered_at' => now(),
            ]);
    }
}
