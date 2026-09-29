<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgendaEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'detail',
        'starts_at',
        'ends_at',
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
}
