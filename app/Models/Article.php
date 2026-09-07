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
        'sale_price_ttc',
        'tva_rate',
        'unit',
        'notes',
        'sort_order',
        'barcode',
        'image_url',
        'weight_kg',
    ];

    protected $appends = ['stock_quantity', 'is_discontinued'];

    public function casts(): array
    {
        return [
            'purchase_price_ht' => 'integer',
            'sale_price_ttc' => 'integer',
            'tva_rate' => 'float',
            'weight_kg' => 'float',
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

    public function attributes(): HasMany
    {
        return $this->hasMany(ArticleAttribute::class);
    }

    public function attributeValue(string $key): ?string
    {
        return $this->attributes()->where('key', $key)->value('value');
    }

    public function getStockQuantityAttribute(): float
    {
        return (float) $this->stockMovements()->sum('quantity');
    }

    public function getIsDiscontinuedAttribute(): bool
    {
        return $this->attributeValue('supplier_status') === 'discontinued';
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('designation');
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        $words = array_filter(preg_split('/\s+/', trim($term)) ?: []);

        foreach ($words as $word) {
            $query->where(function (Builder $q) use ($word) {
                $q->where('reference', 'like', "%{$word}%")
                    ->orWhere('designation', 'like', "%{$word}%")
                    ->orWhere('barcode', 'like', "%{$word}%")
                    ->orWhereHas('subcategory', fn (Builder $sub) => $sub->where('name', 'like', "%{$word}%"))
                    ->orWhereHas('subcategory.category', fn (Builder $cat) => $cat->where('name', 'like', "%{$word}%"));
            });
        }

        return $query;
    }

    public function scopeByBarcode(Builder $query, string $barcode): Builder
    {
        return $query->where('barcode', $barcode);
    }
}
