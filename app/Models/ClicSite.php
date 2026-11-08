<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Clics et pages vues quotidiens du site public : une ligne par jour et par élément (bouton d'accueil, filtre,
 * contact, couleur…). Table du site public (connexion `tracking`), consultée ici en lecture seule.
 * `jour` n'est volontairement pas casté en date : c'est un jour calendaire Y-m-d, comparé tel quel.
 */
class ClicSite extends Model
{
    /** @use HasFactory<\Database\Factories\ClicSiteFactory> */
    use HasFactory;

    protected $connection = 'tracking';

    protected $table = 'clics_site';

    public function scopeBetweenDays(Builder $query, CarbonInterface $start, CarbonInterface $end): Builder
    {
        return $query->whereBetween('jour', [$start->toDateString(), $end->toDateString()]);
    }
}
