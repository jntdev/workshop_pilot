<?php

namespace App\Models;

use App\Enums\QuoteCommentRecipient;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'recipient_label',
        'content',
    ];

    protected function casts(): array
    {
        return [
            'recipient_label' => QuoteCommentRecipient::class,
        ];
    }

    protected static function booted(): void
    {
        static::created(function (QuoteComment $comment) {
            $comment->quote()->update(['comments_resolved_at' => null]);
        });
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }
}
