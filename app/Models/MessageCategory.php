<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MessageCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'label',
        'color',
        'position',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'position' => 'integer',
        ];
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'category_id');
    }

    public static function getDefault(): ?self
    {
        return self::where('is_default', true)->first();
    }

    public static function ordered()
    {
        return self::orderBy('position')->orderBy('label');
    }
}
