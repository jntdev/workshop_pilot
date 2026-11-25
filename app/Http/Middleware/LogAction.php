<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Journal des actions (canal « actions ») : chaque requête qui modifie quelque chose (POST, PUT, PATCH, DELETE),
 * réussie ou non, avec son auteur (utilisateur, API AI, assistant), la route, les fiches visées,
 * les données envoyées (mots de passe, jetons, signatures et fichiers masqués) et le résultat.
 * Les lectures (GET) ne sont pas journalisées.
 */
class LogAction
{
    /** Requêtes de calcul ou de relève automatique, sans effet sur les données : trop fréquentes pour le journal. */
    private const IGNORED_PATHS = [
        'api/quotes/calculate-line',
        'api/quotes/calculate-totals',
        'api/assistant-relay/claim',
    ];

    private const HIDDEN_KEYS = '/pass|token|secret|signature|image|base64|photo|csrf/i';

    private const MAX_STRING = 300;

    private const MAX_INPUT_BYTES = 6000;

    private const MAX_DEPTH = 5;

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethodSafe() || in_array($request->path(), self::IGNORED_PATHS, true)) {
            return $next($request);
        }

        $start = microtime(true);
        $response = $next($request);

        try {
            $this->log($request, $response, (int) round((microtime(true) - $start) * 1000));
        } catch (Throwable $e) {
            // Le journal ne doit jamais empêcher une action d'aboutir.
            report($e);
        }

        return $response;
    }

    private function log(Request $request, Response $response, int $durationMs): void
    {
        $route = $request->route();
        $status = $response->getStatusCode();

        $context = array_filter([
            'actor' => $this->actor($request),
            'action' => $route ? str_replace('App\\Http\\Controllers\\', '', $route->getActionName()) : null,
            'records' => $route ? $this->records($route->parameters()) : [],
            'status' => $status,
            'duration_ms' => $durationMs,
            'result_id' => $this->resultId($response),
            'input' => $this->input($request),
        ], fn ($value) => $value !== null && $value !== []);

        $level = match (true) {
            $status >= 500 => 'error',
            $status >= 400 => 'warning',
            default => 'info',
        };

        Log::channel('actions')->log($level, sprintf('Action : %s /%s → %d', $request->method(), $request->path(), $status), $context);
    }

    private function actor(Request $request): string
    {
        if (auth()->hasUser()) {
            $user = auth()->user();

            return trim('user#'.$user->getAuthIdentifier().' '.($user->name ?? ''));
        }

        $middleware = $request->route()?->gatherMiddleware() ?? [];

        foreach ($middleware as $name) {
            if (! is_string($name)) {
                continue;
            }
            if ($name === 'ai.token') {
                return 'api-ai';
            }
            if (str_starts_with($name, 'assistant.token')) {
                return 'assistant'.(str_contains($name, ':') ? ':'.substr($name, strpos($name, ':') + 1) : '');
            }
        }

        return 'anonyme';
    }

    /**
     * Paramètres de la route : « Quote#613 » pour une fiche, la valeur sinon.
     *
     * @param  array<string, mixed>  $parameters
     * @return array<string, mixed>
     */
    private function records(array $parameters): array
    {
        return array_map(
            fn ($value) => $value instanceof Model ? class_basename($value).'#'.$value->getKey() : (is_scalar($value) ? $value : get_debug_type($value)),
            $parameters,
        );
    }

    /** Identifiant de la fiche créée ou modifiée, quand la réponse JSON le donne. */
    private function resultId(Response $response): int|string|null
    {
        if (! $response instanceof JsonResponse) {
            return null;
        }

        $data = $response->getData(true);
        $id = is_array($data) ? ($data['id'] ?? $data['data']['id'] ?? null) : null;

        return is_int($id) || is_string($id) ? $id : null;
    }

    /**
     * Données envoyées, sans secret ni contenu de fichier, tronquées.
     */
    private function input(Request $request): mixed
    {
        $input = $this->clean($request->all());

        if ($input === []) {
            return null;
        }

        $json = json_encode($input, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);

        return $json !== false && strlen($json) > self::MAX_INPUT_BYTES
            ? mb_strcut($json, 0, self::MAX_INPUT_BYTES).'…'
            : $input;
    }

    private function clean(mixed $value, int $depth = 0): mixed
    {
        if ($value instanceof UploadedFile) {
            return 'fichier '.$value->getClientOriginalName().' ('.$value->getSize().' o)';
        }

        if (is_string($value)) {
            return mb_strimwidth($value, 0, self::MAX_STRING, '…');
        }

        if (! is_array($value)) {
            return $value;
        }

        if ($depth >= self::MAX_DEPTH) {
            return '…';
        }

        $clean = [];
        foreach ($value as $key => $item) {
            $clean[$key] = is_string($key) && preg_match(self::HIDDEN_KEYS, $key) && ! $item instanceof UploadedFile
                ? '[masqué]'
                : $this->clean($item, $depth + 1);
        }

        return $clean;
    }
}
