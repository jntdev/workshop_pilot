<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'needs_order',
        'ordered_at',
        'received_at',
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
            'needs_order' => 'boolean',
            'ordered_at' => 'datetime',
            'received_at' => 'datetime',
        ];
    }

    protected function supplyStatus(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                if ($this->received_at !== null) {
                    return 'received';
                }

                if ($this->ordered_at !== null) {
                    return 'ordered';
                }

                return 'to_order';
            }
        );
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }
}
