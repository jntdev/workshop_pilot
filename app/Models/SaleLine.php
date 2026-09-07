<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'article_id',
        'designation',
        'reference',
        'quantity',
        'purchase_price_ht',
        'unit_price_ttc',
        'tva_rate',
        'line_total_ttc',
        'position',
    ];

    public function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'purchase_price_ht' => 'integer',
            'unit_price_ttc' => 'integer',
            'tva_rate' => 'float',
            'line_total_ttc' => 'integer',
            'position' => 'integer',
        ];
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
