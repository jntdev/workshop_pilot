<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAssistantMessageRequest;
use App\Models\AssistantAction;
use App\Models\AssistantMessage;
use App\Services\Assistant\Actions\AssistantActionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Fenêtre de chat de l'assistant de l'atelier : côté utilisateur connecté.
 * La question est stockée ; le Mac la relève, interroge Claude et renvoie la réponse.
 */
class AssistantChatController extends Controller
{
    public const RELAY_HEARTBEAT_CACHE_KEY = 'assistant.relay.last_seen';

    public function __construct(private readonly AssistantActionService $actions) {}

    /**
     * Échanges d'une conversation de l'utilisateur connecté, du plus ancien au plus récent.
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'conversation_id' => ['required', 'uuid'],
        ]);

        AssistantMessage::failStale();
        AssistantAction::expireStale();

        $messages = AssistantMessage::query()
            ->with('actions.confirmer')
            ->where('user_id', $request->user()->id)
            ->where('conversation_id', $validated['conversation_id'])
            ->orderBy('id')
            ->limit(100)
            ->get()
            ->map(fn (AssistantMessage $message) => $this->payload($message));

        return response()->json($messages);
    }

    public function store(StoreAssistantMessageRequest $request): JsonResponse
    {
        $user = $request->user();

        $unfinished = AssistantMessage::unfinished()->where('user_id', $user->id)->count();

        if ($unfinished >= (int) config('assistant.max_unfinished_per_user')) {
            return response()->json([
                'message' => 'Attends la réponse à tes questions précédentes avant d\'en poser une autre.',
            ], 429);
        }

        $message = AssistantMessage::create([
            'user_id' => $user->id,
            'conversation_id' => $request->validated('conversation_id') ?? (string) Str::uuid(),
            'question' => $request->validated('question'),
        ]);

        return response()->json($this->payload($message), 201);
    }

    public function show(Request $request, AssistantMessage $message): JsonResponse
    {
        abort_unless($message->user_id === $request->user()->id, 404);

        AssistantMessage::failStale();
        AssistantAction::expireStale();

        return response()->json($this->payload($message->refresh()->load('actions.confirmer')));
    }

    /**
     * Indique si le Mac a interrogé le serveur récemment (assistant disponible).
     */
    public function status(): JsonResponse
    {
        $lastSeen = Cache::get(self::RELAY_HEARTBEAT_CACHE_KEY);

        $online = $lastSeen !== null
            && Carbon::parse($lastSeen)->gt(now()->subSeconds((int) config('assistant.relay_online_seconds')));

        return response()->json(['online' => $online]);
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(AssistantMessage $message): array
    {
        return [
            'id' => $message->id,
            'conversation_id' => $message->conversation_id,
            'question' => $message->question,
            'status' => $message->status->value,
            'answer' => $message->answer,
            'error' => $message->error,
            'consultations' => $message->consultations ?? [],
            'actions' => $message->actions->map(fn (AssistantAction $action) => $this->actions->present($action))->values(),
            'created_at' => $message->created_at->toISOString(),
            'answered_at' => $message->answered_at?->toISOString(),
        ];
    }
}
