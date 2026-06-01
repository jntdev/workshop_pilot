<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DemandePartenaire extends Model
{
    use HasFactory;

    protected $fillable = [
        'partenaire_id',
        'date_debut',
        'date_fin',
        'creneau',
        'velos_demandes',
        'commentaire',
        'statut',
    ];

    protected function casts(): array
    {
        return [
            'date_debut' => 'date',
            'date_fin' => 'date',
            'velos_demandes' => 'array',
        ];
    }

    public function partenaire(): BelongsTo
    {
        return $this->belongsTo(Partenaire::class);
    }
}
