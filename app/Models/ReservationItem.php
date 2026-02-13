<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'bike_type_id',
        'quantite',
    ];

    protected function casts(): array
    {
        return [
            'quantite' => 'integer',
        ];
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function bikeType(): BelongsTo
    {
        return $this->belongsTo(BikeType::class);
    }
}
