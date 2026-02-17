<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bike extends Model
{
    use HasFactory;

    protected $fillable = [
        'bike_category_id',
        'bike_size_id',
        'frame_type',
        'model',
        'battery_type',
        'name',
        'status',
        'notes',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'bike_category_id' => 'integer',
            'bike_size_id' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BikeCategory::class, 'bike_category_id');
    }

    public function size(): BelongsTo
    {
        return $this->belongsTo(BikeSize::class, 'bike_size_id');
    }

    /**
     * Génère un identifiant unique pour ce vélo (utilisé comme clé de colonne).
     */
    public function getColumnIdAttribute(): string
    {
        return 'bike_'.$this->id;
    }

    /**
     * Retourne le label complet du type (ex: "VAE M cadre bas").
     */
    public function getTypeLabelAttribute(): string
    {
        $frameLabel = $this->frame_type === 'b' ? 'cadre bas' : 'cadre haut';
        $categoryName = $this->category?->name ?? 'N/A';
        $sizeName = $this->size?->name ?? 'N/A';

        return "{$categoryName} {$sizeName} {$frameLabel}";
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
        return $query->join('bike_categories', 'bikes.bike_category_id', '=', 'bike_categories.id')
            ->join('bike_sizes', 'bikes.bike_size_id', '=', 'bike_sizes.id')
            ->orderBy('bike_categories.sort_order')
            ->orderBy('bike_sizes.sort_order')
            ->orderBy('bikes.frame_type')
            ->orderBy('bikes.sort_order')
            ->select('bikes.*');
    }
}
