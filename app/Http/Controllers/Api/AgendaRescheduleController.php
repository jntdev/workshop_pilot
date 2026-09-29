<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RescheduleDayRequest;
use App\Models\AgendaEvent;
use App\Models\QuoteAppointment;
use App\Services\Agenda\Reschedule\AgendaRescheduler;
use App\Services\Agenda\Reschedule\RescheduleCascadeLimitExceededException;
use App\Services\Agenda\Reschedule\RescheduleMove;
use App\Services\Agenda\Reschedule\RescheduleResult;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AgendaRescheduleController extends Controller
{
    public function __invoke(RescheduleDayRequest $request, AgendaRescheduler $rescheduler): JsonResponse
    {
        $day = CarbonImmutable::parse($request->validated('day'))->startOfDay();

        try {
            $result = DB::transaction(function () use ($day, $rescheduler) {
                $result = $rescheduler->rescheduleDay($day);
                $this->persist($result);

                return $result;
            });
        } catch (RescheduleCascadeLimitExceededException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'moved_count' => $result->movedCount(),
            'days_touched' => $result->distinctDaysCount(),
            'max_cascade_depth' => $result->maxCascadeDepth,
        ]);
    }

    private function persist(RescheduleResult $result): void
    {
        $movesByKind = collect($result->moves)->groupBy('kind');

        foreach ($movesByKind->get('quote', []) as $move) {
            $this->applyMove(QuoteAppointment::query(), $move);
        }

        foreach ($movesByKind->get('event', []) as $move) {
            $this->applyMove(AgendaEvent::query(), $move);
        }
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<QuoteAppointment>|\Illuminate\Database\Eloquent\Builder<AgendaEvent>  $query
     */
    private function applyMove($query, RescheduleMove $move): void
    {
        $query->whereKey($move->id)->update([
            'starts_at' => $move->newStartsAt,
            'ends_at' => $move->newEndsAt,
        ]);
    }
}
