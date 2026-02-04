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
        'quantity',
        'purchase_price_ht',
        'sale_price_ht',
        'sale_price_ttc',
        'margin_amount_ht',
        'margin_rate',
        'tva_rate',
        'line_purchase_ht',
        'line_margin_ht',
        'line_total_ht',
        'line_total_ttc',
        'position',
        'estimated_time_minutes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'purchase_price_ht' => 'decimal:2',
            'sale_price_ht' => 'decimal:2',
            'sale_price_ttc' => 'decimal:2',
            'margin_amount_ht' => 'decimal:2',
            'margin_rate' => 'decimal:4',
            'tva_rate' => 'decimal:4',
            'line_purchase_ht' => 'decimal:2',
            'line_margin_ht' => 'decimal:2',
            'line_total_ht' => 'decimal:2',
            'line_total_ttc' => 'decimal:2',
            'estimated_time_minutes' => 'integer',
        ];
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }
}
