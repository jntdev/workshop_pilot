<?php

namespace App\Services\Assistant\Actions;

use RuntimeException;

/**
 * Refus d'une proposition ou d'une action : le message est en français et destiné à l'utilisateur
 * (et à l'assistant, qui le reformule). Il ne contient jamais de détail technique.
 */
class AssistantActionException extends RuntimeException {}
