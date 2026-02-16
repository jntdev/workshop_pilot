<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bike extends Model
{
    use HasFactory;

    protected $fillable = [
        'bike_type_id',
        'label',
        'status',
        'notes',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function bikeType(): BelongsTo
    {
        return $this->belongsTo(BikeType::class);
    }

    public function scopeOk($query)
    {
        return $query->where('status', 'OK');
    }

    public function scopeHs($query)
    {
        return $query->where('status', 'HS');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
