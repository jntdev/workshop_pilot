<?php

namespace App\Models;

use App\Enums\StockMovementType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id',
        'quantity',
        'type',
        'source_type',
        'source_id',
        'unit_price_ht',
        'note',
    ];

    public function casts(): array
    {
        return [
            'type' => StockMovementType::class,
            'quantity' => 'integer',
            'unit_price_ht' => 'integer',
        ];
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }
}
