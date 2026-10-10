<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Visites quotidiennes d'une fiche vélo du site public : une ligne par jour et par fiche.
 * Table du site public (connexion `tracking`), consultée ici en lecture seule.
 * `jour` n'est volontairement pas casté en date : c'est un jour calendaire Y-m-d, comparé tel quel.
 */
class VisiteFiche extends Model
{
    /** @use HasFactory<\Database\Factories\VisiteFicheFactory> */
    use HasFactory;

    protected $connection = 'tracking';

    protected $table = 'visites_fiches';

    public function scopeBetweenDays(Builder $query, CarbonInterface $start, CarbonInterface $end): Builder
    {
        return $query->whereBetween('jour', [$start->toDateString(), $end->toDateString()]);
    }

    public function scopeOfMarque(Builder $query, ?string $marque): Builder
    {
        return $query->when($marque !== null, fn (Builder $query) => $query->where('marque', $marque));
    }
}
