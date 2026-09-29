<?php

namespace App\Services\Agenda\Reschedule;

use RuntimeException;

/**
 * Levée quand la cascade de report dépasse la limite de sécurité (agenda
 * saturé sur une durée irréaliste). Aucune écriture ne doit avoir eu lieu :
 * l'appelant doit laisser la transaction englobante annuler tout changement.
 */
class RescheduleCascadeLimitExceededException extends RuntimeException
{
    public function __construct(int $limit)
    {
        parent::__construct("Le report a dépassé la limite de {$limit} jours ouvrés consécutifs : l'agenda semble saturé sur une trop longue période.");
    }
}
