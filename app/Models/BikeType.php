<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BikeType extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'category',
        'size',
        'frame_type',
        'label',
        'stock',
    ];

    protected function casts(): array
    {
        return [
            'stock' => 'integer',
        ];
    }

    public function reservationItems(): HasMany
    {
        return $this->hasMany(ReservationItem::class);
    }
}
