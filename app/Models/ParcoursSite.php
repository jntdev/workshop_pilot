<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Tunnels du site public par jour : combien de visites atteignent chaque étape d'un tunnel, dans l'ordre, et par
 * quelle étape elles entrent (parcours `_entree`). Totaux anonymes, sans empreinte ni heure.
 * Table du site public (connexion `tracking`), consultée ici en lecture seule.
 * `jour` n'est volontairement pas casté en date : c'est un jour calendaire Y-m-d, comparé tel quel.
 */
class ParcoursSite extends Model
{
    /** @use HasFactory<\Database\Factories\ParcoursSiteFactory> */
    use HasFactory;

    public const ENTRY_PARCOURS = '_entree';

    protected $connection = 'tracking';

    protected $table = 'parcours_site';

    public function scopeBetweenDays(Builder $query, CarbonInterface $start, CarbonInterface $end): Builder
    {
        return $query->whereBetween('jour', [$start->toDateString(), $end->toDateString()]);
    }
}
