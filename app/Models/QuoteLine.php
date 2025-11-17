<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'title',
        'reference',
        'purchase_price_ht',
        'sale_price_ht',
        'sale_price_ttc',
        'margin_amount_ht',
        'margin_rate',
        'tva_rate',
        'position',
    ];

    protected function casts(): array
    {
        return [
            'purchase_price_ht' => 'decimal:2',
            'sale_price_ht' => 'decimal:2',
            'sale_price_ttc' => 'decimal:2',
            'margin_amount_ht' => 'decimal:2',
            'margin_rate' => 'decimal:4',
            'tva_rate' => 'decimal:4',
        ];
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }
}
