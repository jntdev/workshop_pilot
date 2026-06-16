<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_subcategory_id',
        'brand_id',
        'supplier_id',
        'reference',
        'designation',
        'purchase_price_ht',
        'sale_price_ht',
        'tva_rate',
        'unit',
        'notes',
        'sort_order',
    ];

    protected $appends = ['stock_quantity'];

    public function casts(): array
    {
        return [
            'purchase_price_ht' => 'integer',
            'sale_price_ht' => 'integer',
            'tva_rate' => 'float',
            'sort_order' => 'integer',
        ];
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(ArticleSubcategory::class, 'article_subcategory_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function getStockQuantityAttribute(): int
    {
        return (int) $this->stockMovements()->sum('quantity');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('designation');
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function (Builder $q) use ($term) {
            $q->where('reference', 'like', "%{$term}%")
                ->orWhere('designation', 'like', "%{$term}%");
        });
    }
}
