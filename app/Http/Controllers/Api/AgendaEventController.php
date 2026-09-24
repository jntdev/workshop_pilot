<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAgendaEventRequest;
use App\Http\Requests\UpdateAgendaEventRequest;
use App\Models\AgendaEvent;
use Illuminate\Http\JsonResponse;

class AgendaEventController extends Controller
{
    public function store(StoreAgendaEventRequest $request): JsonResponse
    {
        $event = AgendaEvent::create($request->validated());

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

    protected function formatEvent(AgendaEvent $event): array
    {
        return [
            'kind' => 'event',
            'id' => $event->id,
            'title' => $event->title,
            'detail' => $event->detail,
            'starts_at' => $event->starts_at->toISOString(),
            'ends_at' => $event->ends_at->toISOString(),
        ];
    }
}
