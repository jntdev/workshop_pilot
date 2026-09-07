<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleAttribute extends Model
{
    protected $fillable = [
        'article_id',
        'key',
        'value',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
