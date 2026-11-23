<?php

namespace App\Services\Assistant\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * Les actions de l'assistant passent par le même code que les boutons du workshop
 * (mêmes règles, mêmes refus) : on appelle le contrôleur existant et on traduit ses refus.
 */
trait CallsExistingControllers
{
    /**
     * @param  callable(): mixed  $call
     *
     * @throws AssistantActionException
     */
    protected function callController(callable $call): void
    {
        try {
            $response = $call();
        } catch (ValidationException $exception) {
            throw new AssistantActionException((string) $exception->validator->errors()->first(), 0, $exception);
        }

        if ($response instanceof JsonResponse && $response->getStatusCode() >= 400) {
            $data = $response->getData(true);

            throw new AssistantActionException((string) ($data['message'] ?? 'Action refusée par le workshop.'));
        }
    }
}
