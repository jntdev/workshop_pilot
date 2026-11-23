<?php

namespace App\Http\Controllers\Api\Assistant;

use App\Enums\QuoteStatus;
use App\Http\Controllers\Controller;
use App\Models\AgendaEvent;
use App\Models\Article;
use App\Models\Client;
use App\Models\Quote;
use App\Models\QuoteAppointment;
use App\Models\QuoteLine;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * Lecture seule des données de l'atelier pour l'assistant (jeton ASSISTANT_READ_TOKEN).
 * Uniquement des routes GET. Les prix d'achat et les marges ne sont jamais renvoyés.
 */
class AssistantReadController extends Controller
{
    private const TIMEZONE = 'Europe/Paris';

    private const MAX_AGENDA_DAYS = 62;

    public function clients(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => ['required', 'string', 'min:2', 'max:100'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $clients = Client::query()
            ->where(function (Builder $query) use ($validated) {
                foreach ($this->words($validated['search']) as $word) {
                    $query->where(function (Builder $q) use ($word) {
                        $q->where('prenom', 'like', "%{$word}%")
                            ->orWhere('nom', 'like', "%{$word}%")
                            ->orWhere('telephone', 'like', "%{$word}%")
                            ->orWhere('email', 'like', "%{$word}%");
                    });
                }
            })
            ->orderBy('nom')
            ->orderBy('prenom')
            ->limit($validated['limit'] ?? 20)
            ->get()
            ->map(fn (Client $client) => $this->clientSummary($client));

        return response()->json($clients);
    }

    public function client(int $id): JsonResponse
    {
        $client = Client::query()->findOrFail($id);

        $quotes = $client->quotes()
            ->notArchived()
            ->orderByDesc('id')
            ->limit(30)
            ->get()
            ->map(fn (Quote $quote) => $this->quoteSummary($quote));

        return response()->json([
            ...$this->clientSummary($client),
            'adresse' => $client->adresse,
            'origine_contact' => $client->origine_contact,
            'avantage_type' => $client->avantage_type,
            'avantage_valeur' => $client->avantage_valeur,
            'avantage_expiration' => $client->avantage_expiration?->format('Y-m-d'),
            'quotes' => $quotes,
        ]);
    }

    public function quotes(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'in:'.collect(QuoteStatus::cases())->map(fn (QuoteStatus $s) => $s->value)->join(',')],
            'client_id' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $query = Quote::query()->with('client')->notArchived()->orderByDesc('id');

        if (! empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        if (! empty($validated['client_id'])) {
            $query->where('client_id', $validated['client_id']);
        }

        if (! empty($validated['search'])) {
            foreach ($this->words($validated['search']) as $word) {
                $query->where(function (Builder $q) use ($word) {
                    $q->where('reference', 'like', "%{$word}%")
                        ->orWhere('bike_description', 'like', "%{$word}%")
                        ->orWhere('reception_comment', 'like', "%{$word}%")
                        ->orWhereHas('client', function (Builder $c) use ($word) {
                            $c->where('prenom', 'like', "%{$word}%")
                                ->orWhere('nom', 'like', "%{$word}%")
                                ->orWhere('telephone', 'like', "%{$word}%");
                        });
                });
            }
        }

        $quotes = $query->limit($validated['limit'] ?? 20)->get()
            ->map(fn (Quote $quote) => $this->quoteSummary($quote));

        return response()->json($quotes);
    }

    public function quote(int $id): JsonResponse
    {
        $quote = Quote::query()
            ->with(['client', 'lines', 'comments', 'appointments'])
            ->withSum('payments as paid_total', 'amount')
            ->findOrFail($id);

        return response()->json([
            ...$this->quoteSummary($quote),
            'reception_comment' => $quote->reception_comment,
            'remarks' => $quote->remarks,
            'valid_until' => $quote->valid_until?->format('Y-m-d'),
            'total_ht' => $quote->total_ht,
            'paid_total' => number_format((float) $quote->paid_total, 2, '.', ''),
            'client_notified' => $quote->client_notified,
            'client_notified_at' => $quote->client_notified_at?->format('Y-m-d'),
            'work_completed_notified' => $quote->work_completed_notified,
            'estimated_time_minutes' => $quote->total_estimated_time_minutes,
            'actual_time_minutes' => $quote->actual_time_minutes,
            'client' => $this->clientSummary($quote->client),
            'lines' => $quote->lines->map(fn ($line) => [
                'id' => $line->id,
                'title' => $line->title,
                'reference' => $line->reference,
                'quantity' => $line->quantity,
                'sale_price_ttc' => $line->sale_price_ttc,
                'line_total_ttc' => $line->line_total_ttc,
                'estimated_time_minutes' => $line->estimated_time_minutes,
                'needs_order' => (bool) $line->needs_order,
                'order_status' => $line->needs_order ? $this->orderStatus($line) : null,
                'ordered_at' => $line->ordered_at?->format('Y-m-d'),
                'received_at' => $line->received_at?->format('Y-m-d'),
            ])->values(),
            'comments' => $quote->comments->map(fn ($comment) => [
                'recipient' => $comment->recipient_label,
                'content' => $comment->content,
                'created_at' => $this->local($comment->created_at),
            ])->values(),
            'appointments' => $quote->appointments->map(fn (QuoteAppointment $appointment) => [
                'id' => $appointment->id,
                'starts_at' => $this->local($appointment->starts_at),
                'ends_at' => $this->local($appointment->ends_at),
                'notes' => $appointment->notes,
                'completed' => (bool) $appointment->completed,
            ])->values(),
        ]);
    }

    /**
     * Rendez-vous des devis et événements libres entre deux dates (heures de Paris).
     */
    public function agenda(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'start' => ['required', 'date'],
            'end' => ['required', 'date', 'after_or_equal:start'],
        ]);

        $start = Carbon::parse($validated['start'], self::TIMEZONE)->startOfDay();
        $end = Carbon::parse($validated['end'], self::TIMEZONE)->endOfDay();

        abort_if($start->diffInDays($end) > self::MAX_AGENDA_DAYS, 422, 'Période trop longue (62 jours au plus).');

        $appointments = QuoteAppointment::query()
            ->with('quote.client')
            ->whereBetween('starts_at', [$start->copy()->utc(), $end->copy()->utc()])
            ->get()
            ->map(fn (QuoteAppointment $appointment) => [
                'kind' => 'devis',
                'id' => $appointment->id,
                'sort' => $appointment->starts_at->toISOString(),
                'quote_id' => $appointment->quote_id,
                'quote_reference' => $appointment->quote->reference,
                'client' => trim($appointment->quote->client->prenom.' '.$appointment->quote->client->nom),
                'bike_description' => $appointment->quote->bike_description,
                'status_label' => $appointment->quote->status?->label(),
                'starts_at' => $this->local($appointment->starts_at),
                'ends_at' => $this->local($appointment->ends_at),
                'notes' => $appointment->notes,
                'completed' => (bool) $appointment->completed,
            ]);

        $events = AgendaEvent::query()
            ->whereBetween('starts_at', [$start->copy()->utc(), $end->copy()->utc()])
            ->get()
            ->map(fn (AgendaEvent $event) => [
                'kind' => 'evenement',
                'id' => $event->id,
                'sort' => $event->starts_at->toISOString(),
                'title' => $event->title,
                'detail' => $event->detail,
                'starts_at' => $this->local($event->starts_at),
                'ends_at' => $this->local($event->ends_at),
                'completed' => (bool) $event->completed,
            ]);

        $items = $appointments->concat($events)->sortBy('sort')->map(function (array $item) {
            unset($item['sort']);

            return $item;
        })->values();

        return response()->json($items);
    }

    public function articles(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => ['required', 'string', 'min:2', 'max:100'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:30'],
        ]);

        $articles = Article::query()
            ->with('brand')
            ->withSum('stockMovements', 'quantity')
            ->search($validated['search'])
            ->ordered()
            ->limit($validated['limit'] ?? 15)
            ->get()
            ->map(fn (Article $article) => [
                'id' => $article->id,
                'reference' => $article->reference,
                'designation' => $article->designation,
                'brand' => $article->brand?->name,
                'sale_price_ttc_eur' => number_format($article->sale_price_ttc / 100, 2, '.', ''),
                'unit' => $article->unit,
                'stock_quantity' => (float) ($article->stock_movements_sum_quantity ?? 0),
                'notes' => $article->notes,
            ]);

        return response()->json($articles);
    }

    /**
     * Pièces à commander des devis (page « Pièces à commander »), avec le numéro de ligne
     * nécessaire pour proposer de les marquer commandées ou reçues.
     */
    public function orderLines(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'include_received' => ['nullable', 'boolean'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = QuoteLine::query()->with('quote.client')->where('needs_order', true);

        if (! $request->boolean('include_received')) {
            $query->whereNull('received_at');
        }

        $lines = $query->orderBy('created_at')->limit($validated['limit'] ?? 50)->get()
            ->map(fn (QuoteLine $line) => [
                'line_id' => $line->id,
                'quote_id' => $line->quote_id,
                'quote_reference' => $line->quote?->reference,
                'client' => $line->quote?->client ? trim($line->quote->client->prenom.' '.$line->quote->client->nom) : null,
                'bike_description' => $line->quote?->bike_description,
                'title' => $line->title,
                'reference' => $line->reference,
                'quantity' => $line->quantity,
                'order_status' => $this->orderStatus($line),
                'ordered_at' => $line->ordered_at?->format('Y-m-d'),
                'received_at' => $line->received_at?->format('Y-m-d'),
            ]);

        return response()->json($lines);
    }

    private function orderStatus(QuoteLine $line): string
    {
        if ($line->received_at !== null) {
            return 'received';
        }

        return $line->ordered_at !== null ? 'ordered' : 'to_order';
    }

    /**
     * @return list<string>
     */
    private function words(string $search): array
    {
        $escaped = addcslashes(trim($search), '%_\\');

        return array_values(array_filter(preg_split('/\s+/', $escaped) ?: []));
    }

    /**
     * @return array<string, mixed>
     */
    private function clientSummary(Client $client): array
    {
        return [
            'id' => $client->id,
            'prenom' => $client->prenom,
            'nom' => $client->nom,
            'telephone' => $client->telephone,
            'email' => $client->email,
            'commentaires' => $client->commentaires,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function quoteSummary(Quote $quote): array
    {
        return [
            'id' => $quote->id,
            'reference' => $quote->reference,
            'client_id' => $quote->client_id,
            'client_name' => $quote->relationLoaded('client') && $quote->client
                ? trim($quote->client->prenom.' '.$quote->client->nom)
                : null,
            'bike_description' => $quote->bike_description,
            'status' => $quote->status?->value,
            'status_label' => $quote->status?->label(),
            'total_ttc' => $quote->total_ttc,
            'created_at' => $this->local($quote->created_at),
        ];
    }

    private function local(?Carbon $date): ?string
    {
        return $date?->copy()->timezone(self::TIMEZONE)->format('d/m/Y H:i');
    }
}
