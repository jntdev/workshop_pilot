<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAgendaEventRequest;
use App\Http\Requests\UpdateAgendaEventRequest;
use App\Models\AgendaEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AgendaEventController extends Controller
{
    public function store(StoreAgendaEventRequest $request): JsonResponse
    {
        $event = AgendaEvent::create($request->validated())->refresh();

        return response()->json($this->formatEvent($event), 201);
    }

    public function update(UpdateAgendaEventRequest $request, AgendaEvent $agendaEvent): JsonResponse
    {
        $agendaEvent->update($request->validated());

        return response()->json($this->formatEvent($agendaEvent));
    }

    public function destroy(AgendaEvent $agendaEvent): JsonResponse
    {
        $agendaEvent->delete();

        return response()->json(null, 204);
    }

    public function updateFlags(Request $request, AgendaEvent $agendaEvent): JsonResponse
    {
        $validated = $request->validate([
            'completed' => ['sometimes', 'boolean'],
            'is_appointment' => ['sometimes', 'boolean'],
            'is_commitment' => ['sometimes', 'boolean'],
        ]);

        $agendaEvent->update($this->applyMutuallyExclusiveFlags($validated));

        return response()->json($this->formatEvent($agendaEvent));
    }

    /**
     * Rendez-vous et Engagement sont mutuellement exclusifs : activer l'un
     * désactive l'autre, quelle que soit la combinaison envoyée par le client.
     *
     * @param  array<string, bool>  $flags
     * @return array<string, bool>
     */
    private function applyMutuallyExclusiveFlags(array $flags): array
    {
        if (($flags['is_appointment'] ?? false) === true) {
            $flags['is_commitment'] = false;
        } elseif (($flags['is_commitment'] ?? false) === true) {
            $flags['is_appointment'] = false;
        }

        return $flags;
    }

    protected function formatEvent(AgendaEvent $event): array
    {
        return [
            'kind' => 'event',
            'id' => $event->id,
            'title' => $event->title,
            'detail' => $event->detail,
            'starts_at' => $event->starts_at->toISOString(),
            'ends_at' => $event->ends_at->toISOString(),
            'completed' => $event->completed,
            'is_appointment' => $event->is_appointment,
            'is_commitment' => $event->is_commitment,
        ];
    }
}
