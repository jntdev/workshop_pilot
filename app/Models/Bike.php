<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bike extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'size',
        'frame_type',
        'name',
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

    /**
     * Génère un identifiant unique pour ce vélo (utilisé comme clé de colonne).
     */
    public function getColumnIdAttribute(): string
    {
        return 'bike_' . $this->id;
    }

    /**
     * Retourne le label complet du type (ex: "VAE M cadre bas").
     */
    public function getTypeLabelAttribute(): string
    {
        $frameLabel = $this->frame_type === 'b' ? 'cadre bas' : 'cadre haut';
        return "{$this->category} {$this->size} {$frameLabel}";
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
        return $query->orderBy('category')
            ->orderByRaw("FIELD(size, 'S', 'M', 'L', 'XL')")
            ->orderBy('frame_type')
            ->orderBy('sort_order');
    }
}
