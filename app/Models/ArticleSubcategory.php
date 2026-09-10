<?php

namespace App\Models;

use App\Enums\ArticleCategorySource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ArticleSubcategory extends Model
{
    use HasFactory;

    protected $fillable = ['article_category_id', 'name', 'sort_order'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ArticleCategory::class, 'article_category_id');
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class)->ordered();
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function isManual(): bool
    {
        return $this->category->source === ArticleCategorySource::Manual;
    }
}
