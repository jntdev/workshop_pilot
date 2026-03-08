<?php

namespace App\Models;

use App\Enums\WorkMode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'author_mode',
        'recipient_mode',
        'category',
        'contact_name',
        'contact_phone',
        'contact_email',
        'content',
        'status',
        'read_at',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'author_mode' => WorkMode::class,
            'recipient_mode' => WorkMode::class,
            'read_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function replies(): HasMany
    {
        return $this->hasMany(MessageReply::class)->orderBy('created_at');
    }

    public const CATEGORIES = ['accueil', 'atelier', 'location', 'autre'];

    /**
     * Compte les messages non lus pour un mode donné.
     * Exclut les notes perso (recipient_mode = null) car elles n'ont pas de statut "lu".
     */
    public static function unreadCountForMode(WorkMode|string $mode): int
    {
        if (is_string($mode)) {
            $mode = WorkMode::from($mode);
        }

        return self::where('status', 'ouvert')
            ->whereNull('read_at')
            ->whereNotNull('recipient_mode') // Exclure les notes perso
            ->where('recipient_mode', $mode->value)
            ->count();
    }

    /**
     * Compte les messages non lus par catégorie pour un mode donné.
     * Exclut les notes perso (recipient_mode = null) car elles n'ont pas de statut "lu".
     *
     * @return array<string, int>
     */
    public static function unreadCountByCategoryForMode(WorkMode|string $mode): array
    {
        if (is_string($mode)) {
            $mode = WorkMode::from($mode);
        }

        $counts = self::where('status', 'ouvert')
            ->whereNull('read_at')
            ->whereNotNull('recipient_mode') // Exclure les notes perso
            ->where('recipient_mode', $mode->value)
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();

        // Assurer que toutes les catégories sont présentes
        $result = [];
        foreach (self::CATEGORIES as $cat) {
            $result[$cat] = $counts[$cat] ?? 0;
        }

        return $result;
    }

    /**
     * Messages pour un mode donné :
     * - Messages destinés à ce mode
     * - Messages créés par ce mode (envoyés ou notes perso)
     */
    public static function forMode(WorkMode|string $mode)
    {
        if (is_string($mode)) {
            $mode = WorkMode::from($mode);
        }

        return self::where(function ($q) use ($mode) {
            $q->where('recipient_mode', $mode->value)
                ->orWhere('author_mode', $mode->value);
        })
            ->orderByDesc('created_at');
    }

    public function markAsRead(): void
    {
        if ($this->read_at === null) {
            $this->update(['read_at' => now()]);
        }
    }

    public function markAsResolved(): void
    {
        $this->update([
            'status' => 'resolu',
            'resolved_at' => now(),
        ]);
    }

    public function reopen(): void
    {
        $this->update([
            'status' => 'ouvert',
            'resolved_at' => null,
        ]);
    }

    public function isResolved(): bool
    {
        return $this->status === 'resolu';
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
