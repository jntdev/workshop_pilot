<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UploadToken extends Model
{
    /** @use HasFactory<\Database\Factories\UploadTokenFactory> */
    use HasFactory;

    protected $fillable = [
        'token',
        'context_type',
        'context_id',
        'expires_at',
        'used_count',
        'max_uses',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used_count' => 'integer',
            'max_uses' => 'integer',
        ];
    }

    public function scopeValid(Builder $query): Builder
    {
        return $query->where('expires_at', '>', now())
            ->whereColumn('used_count', '<', 'max_uses');
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('expires_at', '<=', now())
                ->orWhereColumn('used_count', '>=', 'max_uses');
        });
    }

    public static function generate(
        string $contextType,
        ?int $contextId = null,
        int $maxUses = 10,
        int $expiresInMinutes = 15
    ): self {
        return self::create([
            'token' => Str::uuid()->toString(),
            'context_type' => $contextType,
            'context_id' => $contextId,
            'expires_at' => now()->addMinutes($expiresInMinutes),
            'max_uses' => $maxUses,
        ]);
    }

    public static function consume(string $token): ?self
    {
        $uploadToken = self::valid()
            ->where('token', $token)
            ->first();

        if (! $uploadToken) {
            return null;
        }

        $uploadToken->increment('used_count');

        return $uploadToken;
    }

    public function isValid(): bool
    {
        return $this->expires_at->isFuture()
            && $this->used_count < $this->max_uses;
    }

    public function remainingUses(): int
    {
        return max(0, $this->max_uses - $this->used_count);
    }
}
