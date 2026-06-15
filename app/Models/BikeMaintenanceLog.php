<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BikeMaintenanceLog extends Model
{
    protected $appends = ['supply_status'];

    protected $fillable = [
        'bike_id',
        'article_id',
        'date',
        'description',
        'reference',
        'cost',
        'duration_minutes',
        'status',
        'needs_order',
        'ordered_at',
        'received_at',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'cost' => 'integer',
            'duration_minutes' => 'integer',
            'needs_order' => 'boolean',
            'ordered_at' => 'datetime',
            'received_at' => 'datetime',
        ];
    }

    public function bike(): BelongsTo
    {
        return $this->belongsTo(Bike::class);
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function getSupplyStatusAttribute(): string
    {
        if ($this->received_at !== null) {
            return 'received';
        }

        if ($this->ordered_at !== null) {
            return 'ordered';
        }

        return 'to_order';
    }
}
