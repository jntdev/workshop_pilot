<?php

namespace App\Services\Assistant\Actions;

use App\Enums\AssistantActionStatus;
use App\Enums\AssistantActionType;
use App\Models\AgendaEvent;
use App\Models\AssistantAction;
use App\Models\Quote;
use App\Models\QuoteAppointment;
use App\Services\Agenda\OpeningHours\OpeningHours;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Déplacer un rendez-vous de l'agenda de réparation (rendez-vous d'un devis ou événement libre).
 * Les heures sont données en heure de Paris, la base les garde en UTC. Le déplacement n'est jamais
 * bloqué par les horaires ou un chevauchement (comme l'agenda actuel) : il est signalé en avertissement.
 */
class AppointmentMoveHandler implements AssistantActionHandler
{
    private const TIMEZONE = 'Europe/Paris';

    private const DATABASE_FORMAT = 'Y-m-d H:i:s';

    private const DAYS = ['dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];

    private const SUBJECT_TYPES = ['devis' => 'quote_appointment', 'evenement' => 'agenda_event'];

    public function __construct(private readonly OpeningHours $openingHours) {}

    public function propose(array $params): ActionProposal
    {
        $validator = Validator::make($params, [
            'kind' => ['required', 'string', Rule::in(array_keys(self::SUBJECT_TYPES))],
            'id' => ['required', 'integer'],
            'starts_at' => ['required', 'date_format:Y-m-d H:i'],
            'ends_at' => ['nullable', 'date_format:Y-m-d H:i'],
        ], [
            'kind.in' => 'Type inconnu : « devis » (rendez-vous d\'un devis) ou « evenement » (événement libre).',
            'id.required' => 'Il manque le numéro du rendez-vous.',
            'id.integer' => 'Le numéro du rendez-vous doit être un nombre.',
            'starts_at.required' => 'Il manque le nouveau début.',
            'starts_at.date_format' => 'Format attendu pour le début : AAAA-MM-JJ HH:MM (heure de Paris).',
            'ends_at.date_format' => 'Format attendu pour la fin : AAAA-MM-JJ HH:MM (heure de Paris).',
        ]);

        if ($validator->fails()) {
            throw new AssistantActionException((string) $validator->errors()->first());
        }

        $kind = $params['kind'];
        $item = $this->find(self::SUBJECT_TYPES[$kind], (int) $params['id']);

        $newStart = Carbon::createFromFormat('Y-m-d H:i', $params['starts_at'], self::TIMEZONE);
        $duration = $item->ends_at->getTimestamp() - $item->starts_at->getTimestamp();
        $newEnd = ! empty($params['ends_at'])
            ? Carbon::createFromFormat('Y-m-d H:i', $params['ends_at'], self::TIMEZONE)
            : $newStart->copy()->addSeconds($duration);

        if ($newEnd->lessThanOrEqualTo($newStart)) {
            throw new AssistantActionException('La fin doit être après le début.');
        }

        if (abs($newStart->diffInDays(now(), false)) > 730) {
            throw new AssistantActionException('Cette date est trop éloignée (plus de deux ans).');
        }

        $startUtc = $newStart->copy()->utc();
        $endUtc = $newEnd->copy()->utc();

        if ($startUtc->equalTo($item->starts_at) && $endUtc->equalTo($item->ends_at)) {
            throw new AssistantActionException('Ce rendez-vous est déjà à ce moment-là.');
        }

        $before = [
            'starts_at' => $item->starts_at->copy()->utc()->format(self::DATABASE_FORMAT),
            'ends_at' => $item->ends_at->copy()->utc()->format(self::DATABASE_FORMAT),
        ];

        if ($item instanceof QuoteAppointment) {
            $before['quote_id'] = $item->quote_id;
            $label = sprintf('Rendez-vous du devis %s (%s)', $item->quote?->reference ?? '?', $this->describe($item->quote));
        } else {
            $label = sprintf('Événement « %s »', $item->title);
        }

        return new ActionProposal(
            type: AssistantActionType::AppointmentMove,
            subjectType: self::SUBJECT_TYPES[$kind],
            subjectId: $item->id,
            payload: [
                'starts_at' => $startUtc->format(self::DATABASE_FORMAT),
                'ends_at' => $endUtc->format(self::DATABASE_FORMAT),
            ],
            before: $before,
            summary: sprintf('%s : %s → %s', $label, $this->range($item->starts_at, $item->ends_at), $this->range($startUtc, $endUtc)),
            warnings: $this->warnings($item, $newStart, $newEnd),
        );
    }

    public function execute(AssistantAction $action): array
    {
        $item = $this->find($action->subject_type, $action->subject_id);

        if (! $this->isAt($item, $action->before)) {
            throw new AssistantActionException('Le rendez-vous a été déplacé depuis la proposition. Redemande à l\'assistant.');
        }

        $item->update([
            'starts_at' => Carbon::parse($action->payload['starts_at'], 'UTC'),
            'ends_at' => Carbon::parse($action->payload['ends_at'], 'UTC'),
        ]);

        return $this->times($item->refresh());
    }

    public function canUndo(AssistantAction $action): bool
    {
        if ($action->status !== AssistantActionStatus::Executed) {
            return false;
        }

        $item = $this->findOrNull($action->subject_type, $action->subject_id);

        return $item !== null && $this->isAt($item, $action->payload);
    }

    public function undo(AssistantAction $action): array
    {
        $item = $this->findOrNull($action->subject_type, $action->subject_id);

        if ($item === null || ! $this->isAt($item, $action->payload)) {
            throw new AssistantActionException('Le rendez-vous a encore été déplacé depuis : impossible de défaire sans risque.');
        }

        $item->update([
            'starts_at' => Carbon::parse($action->before['starts_at'], 'UTC'),
            'ends_at' => Carbon::parse($action->before['ends_at'], 'UTC'),
        ]);

        return $this->times($item->refresh());
    }

    public function link(AssistantAction $action): ?string
    {
        $quoteId = $action->before['quote_id'] ?? null;

        return $quoteId ? '/atelier/devis/'.$quoteId : '/atelier/agenda';
    }

    /**
     * @throws AssistantActionException
     */
    private function find(string $subjectType, int $id): QuoteAppointment|AgendaEvent
    {
        return $this->findOrNull($subjectType, $id)
            ?? throw new AssistantActionException('Rendez-vous introuvable.');
    }

    private function findOrNull(string $subjectType, int $id): QuoteAppointment|AgendaEvent|null
    {
        return $subjectType === 'quote_appointment'
            ? QuoteAppointment::query()->with('quote.client')->find($id)
            : AgendaEvent::query()->find($id);
    }

    /**
     * @param  array<string, mixed>  $times  clés starts_at et ends_at (UTC, Y-m-d H:i:s)
     */
    private function isAt(QuoteAppointment|AgendaEvent $item, array $times): bool
    {
        return $item->starts_at->copy()->utc()->format(self::DATABASE_FORMAT) === ($times['starts_at'] ?? null)
            && $item->ends_at->copy()->utc()->format(self::DATABASE_FORMAT) === ($times['ends_at'] ?? null);
    }

    /**
     * @return array<string, string>
     */
    private function times(QuoteAppointment|AgendaEvent $item): array
    {
        return [
            'starts_at' => $item->starts_at->copy()->utc()->format(self::DATABASE_FORMAT),
            'ends_at' => $item->ends_at->copy()->utc()->format(self::DATABASE_FORMAT),
        ];
    }

    /**
     * @return list<string>
     */
    private function warnings(QuoteAppointment|AgendaEvent $item, Carbon $newStart, Carbon $newEnd): array
    {
        $warnings = [];

        if ($item->completed) {
            $warnings[] = 'Ce rendez-vous est marqué « terminé ».';
        }

        if ($newStart->isPast()) {
            $warnings[] = 'Ce créneau est dans le passé.';
        }

        if (! $this->openingHours->isOpenDay($newStart)) {
            $warnings[] = 'L\'atelier est fermé ce jour-là (dimanche et lundi).';
        } elseif (! $newStart->isSameDay($newEnd) || ! $this->openingHours->fitsWithoutCrossingLunch($newStart, $newEnd)) {
            $warnings[] = 'Hors des heures d\'ouverture (9h-12h et 14h-18h), ou à cheval sur la pause déjeuner.';
        }

        $overlaps = $this->overlaps($item, $newStart->copy()->utc(), $newEnd->copy()->utc());

        if ($overlaps !== []) {
            $warnings[] = 'Chevauche : '.implode(' ; ', $overlaps).'.';
        }

        return $warnings;
    }

    /**
     * @return list<string>
     */
    private function overlaps(QuoteAppointment|AgendaEvent $item, Carbon $startUtc, Carbon $endUtc): array
    {
        $appointments = QuoteAppointment::query()
            ->with('quote.client')
            ->where('starts_at', '<', $endUtc)
            ->where('ends_at', '>', $startUtc)
            ->when($item instanceof QuoteAppointment, fn ($query) => $query->whereKeyNot($item->id))
            ->limit(3)
            ->get()
            ->map(fn (QuoteAppointment $other) => sprintf(
                'devis %s (%s) %s',
                $other->quote?->reference ?? '?',
                $this->describe($other->quote),
                $this->hours($other->starts_at, $other->ends_at),
            ));

        $events = AgendaEvent::query()
            ->where('starts_at', '<', $endUtc)
            ->where('ends_at', '>', $startUtc)
            ->when($item instanceof AgendaEvent, fn ($query) => $query->whereKeyNot($item->id))
            ->limit(3)
            ->get()
            ->map(fn (AgendaEvent $other) => sprintf('« %s » %s', $other->title, $this->hours($other->starts_at, $other->ends_at)));

        return $appointments->concat($events)->take(3)->values()->all();
    }

    private function describe(?Quote $quote): string
    {
        if ($quote === null) {
            return 'devis inconnu';
        }

        $client = $quote->client ? trim($quote->client->prenom.' '.$quote->client->nom) : 'client inconnu';

        return $quote->bike_description ? "{$client}, {$quote->bike_description}" : $client;
    }

    private function hours(Carbon $start, Carbon $end): string
    {
        return $start->copy()->timezone(self::TIMEZONE)->format('H:i').'-'.$end->copy()->timezone(self::TIMEZONE)->format('H:i');
    }

    private function range(Carbon $start, Carbon $end): string
    {
        $a = $start->copy()->timezone(self::TIMEZONE);
        $b = $end->copy()->timezone(self::TIMEZONE);
        $first = self::DAYS[$a->dayOfWeek].' '.$a->format('d/m');

        if ($a->isSameDay($b)) {
            return "{$first} {$a->format('H:i')}-{$b->format('H:i')}";
        }

        return "{$first} {$a->format('H:i')} → ".self::DAYS[$b->dayOfWeek].' '.$b->format('d/m').' '.$b->format('H:i');
    }
}
