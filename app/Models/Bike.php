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
        $categoryName = $this->category?->name ?? 'N/A';
        $parts = [$categoryName];

        if ($this->size) {
            $parts[] = $this->size->name;
        }

        if ($this->frame_type) {
            $parts[] = $this->frame_type === 'b' ? 'cadre bas' : 'cadre haut';
        }

        return implode(' ', $parts);
    }

    /**
     * Génère l'identifiant du bike_type correspondant à ce vélo.
     */
    public function getBikeTypeIdAttribute(): string
    {
        $categoryName = $this->category?->name ?? 'UNKNOWN';
        $sizePart = $this->size ? strtolower($this->size->name) : '';
        $framePart = $this->frame_type ?? '';

        return $categoryName.'_'.$sizePart.$framePart;
    }

    /**
     * Synchronise l'entrée bike_types pour ce type de vélo (crée ou met à jour le stock).
     */
    public function syncBikeType(): void
    {
        $this->loadMissing(['category', 'size']);

        $typeId = $this->bike_type_id;

        $stock = Bike::where('bike_category_id', $this->bike_category_id)
            ->where(function ($q) {
                if ($this->bike_size_id) {
                    $q->where('bike_size_id', $this->bike_size_id);
                } else {
                    $q->whereNull('bike_size_id');
                }
            })
            ->where(function ($q) {
                if ($this->frame_type) {
                    $q->where('frame_type', $this->frame_type);
                } else {
                    $q->whereNull('frame_type');
                }
            })
            ->count();

        BikeType::updateOrCreate(
            ['id' => $typeId],
            [
                'category' => $this->category?->name ?? 'UNKNOWN',
                'size' => $this->size?->name ?? null,
                'frame_type' => $this->frame_type,
                'label' => $this->type_label,
                'stock' => $stock,
            ]
        );
    }

    /**
     * Synchronise le stock du bike_type après suppression d'un vélo.
     * Si le stock tombe à 0, supprime le bike_type.
     *
     * @param  array{type_id: string, category_id: int, size_id: int|null, frame_type: string|null}  $context
     */
    public static function syncBikeTypeAfterDelete(array $context): void
    {
        $stock = Bike::where('bike_category_id', $context['category_id'])
            ->where(function ($q) use ($context) {
                if ($context['size_id']) {
                    $q->where('bike_size_id', $context['size_id']);
                } else {
                    $q->whereNull('bike_size_id');
                }
            })
            ->where(function ($q) use ($context) {
                if ($context['frame_type']) {
                    $q->where('frame_type', $context['frame_type']);
                } else {
                    $q->whereNull('frame_type');
                }
            })
            ->count();

        if ($stock === 0) {
            BikeType::where('id', $context['type_id'])->delete();
        } else {
            BikeType::where('id', $context['type_id'])->update(['stock' => $stock]);
        }
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
            ->leftJoin('bike_sizes', 'bikes.bike_size_id', '=', 'bike_sizes.id')
            ->orderBy('bike_categories.sort_order')
            ->orderByRaw('bike_sizes.sort_order IS NULL, bike_sizes.sort_order')
            ->orderByRaw('bikes.frame_type IS NULL, bikes.frame_type')
            ->orderBy('bikes.sort_order')
            ->select('bikes.*');
    }
}
