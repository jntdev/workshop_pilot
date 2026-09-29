<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteAppointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'starts_at',
        'ends_at',
        'notes',
        'completed',
        'is_appointment',
        'is_commitment',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'completed' => 'boolean',
            'is_appointment' => 'boolean',
            'is_commitment' => 'boolean',
        ];
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }
}
