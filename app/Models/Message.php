<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Message extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'author_user_id',
        'recipient_user_id',
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
            'read_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_user_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(MessageReply::class)->orderBy('created_at');
    }

    public const CATEGORIES = ['accueil', 'atelier', 'location', 'autre'];

    public static function unreadCountForUser(int $userId): int
    {
        $unreadMessages = self::where('status', 'ouvert')
            ->whereNull('read_at')
            ->whereNotNull('recipient_user_id')
            ->where('recipient_user_id', $userId)
            ->count();

        $unreadReplies = MessageReply::whereNull('read_at')
            ->where('author_user_id', '!=', $userId)
            ->whereHas('message', function ($q) use ($userId) {
                $q->where('author_user_id', $userId)
                    ->orWhere('recipient_user_id', $userId);
            })
            ->count();

        return $unreadMessages + $unreadReplies;
    }

    /**
     * @return array<string, int>
     */
    public static function unreadCountByCategoryForUser(int $userId): array
    {
        $counts = self::where('status', 'ouvert')
            ->whereNull('read_at')
            ->whereNotNull('recipient_user_id')
            ->where('recipient_user_id', $userId)
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();

        $result = [];
        foreach (self::CATEGORIES as $cat) {
            $result[$cat] = $counts[$cat] ?? 0;
        }

        return $result;
    }

    public static function forUser(int $userId)
    {
        return self::where(function ($q) use ($userId) {
            $q->where('recipient_user_id', $userId)
                ->orWhere('author_user_id', $userId);
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
        return $this->author->name;
    }

    public function recipientLabel(): ?string
    {
        return $this->recipient?->name;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photos')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/heic']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        // Désactivé sur serveur mutualisé sans GD/Imagick
        // $this->addMediaConversion('thumb')
        //     ->width(300)
        //     ->height(300)
        //     ->sharpen(10)
        //     ->nonQueued();
    }
}
