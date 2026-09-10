<?php

namespace App\Models;

use App\Enums\ArticleCategorySource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ArticleCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'sort_order', 'source'];

    protected function casts(): array
    {
        return [
            'source' => ArticleCategorySource::class,
        ];
    }

    public function subcategories(): HasMany
    {
        return $this->hasMany(ArticleSubcategory::class)->ordered();
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function scopeManual(Builder $query): Builder
    {
        return $query->where('source', ArticleCategorySource::Manual);
    }

    public function scopeCatalogue(Builder $query): Builder
    {
        return $query->where('source', ArticleCategorySource::Catalogue);
    }
}
