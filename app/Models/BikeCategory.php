<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BikeCategory extends Model
{
    protected $fillable = [
        'name',
        'color',
        'has_battery',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'has_battery' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function bikes(): HasMany
    {
        return $this->hasMany(Bike::class, 'bike_category_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
