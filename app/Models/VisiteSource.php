<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * D'où viennent les visites du site public : une ligne par jour, par cible (`accueil` ou la fiche d'un vélo) et par
 * catégorie d'origine (moteur, réseau social, QR code…). Jamais l'adresse du site d'origine.
 * Table du site public (connexion `tracking`), consultée ici en lecture seule.
 * `jour` n'est volontairement pas casté en date : c'est un jour calendaire Y-m-d, comparé tel quel.
 */
class VisiteSource extends Model
{
    /** @use HasFactory<\Database\Factories\VisiteSourceFactory> */
    use HasFactory;

    public const HOME_TARGET = 'accueil';

    protected $connection = 'tracking';

    protected $table = 'visites_sources';

    public function scopeBetweenDays(Builder $query, CarbonInterface $start, CarbonInterface $end): Builder
    {
        return $query->whereBetween('jour', [$start->toDateString(), $end->toDateString()]);
    }
}
