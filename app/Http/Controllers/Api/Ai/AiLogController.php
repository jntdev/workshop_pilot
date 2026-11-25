<?php

namespace App\Http\Controllers\Api\Ai;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Generator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Journaux de l'application (storage/logs/laravel*.log) pour l'API AI, en lecture seule :
 * erreurs, avertissements et appels à l'API AI, du plus récent au plus ancien.
 * Sert par exemple à vérifier si une sauvegarde a échoué ou si une route AI a modifié un devis.
 */
class AiLogController extends Controller
{
    /** Niveaux Monolog, du moins grave au plus grave. */
    private const LEVELS = [
        'debug' => 100,
        'info' => 200,
        'notice' => 250,
        'warning' => 300,
        'error' => 400,
        'critical' => 500,
        'alert' => 550,
        'emergency' => 600,
    ];

    /** Taille lue au plus par requête, tous fichiers confondus, pour rester rapide sur l'hébergement mutualisé. */
    private const MAX_SCAN_BYTES = 64 * 1024 * 1024;

    private const CHUNK_BYTES = 65536;

    private const MAX_MESSAGE_CHARS = 2000;

    private const MAX_TRACE_LINES = 20;

    private const HEADER = '/^\[(\d{4}-\d{2}-\d{2}[ T]\d{2}:\d{2}:\d{2})[^\]]*\] ([\w-]+)\.(\w+): ?(.*)$/s';

    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'level' => ['nullable', 'string', 'in:'.implode(',', array_keys(self::LEVELS))],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:500'],
        ]);

        $limit = (int) ($validated['limit'] ?? 100);
        $search = isset($validated['search']) ? mb_strtolower($validated['search']) : null;
        $minLevel = isset($validated['level']) ? self::LEVELS[$validated['level']] : null;
        $from = isset($validated['from']) ? Carbon::parse($validated['from'])->format('Y-m-d H:i:s') : null;
        $to = isset($validated['to']) ? $this->endOfPeriod($validated['to']) : null;

        $files = $this->logFiles();
        $entries = [];
        $budget = self::MAX_SCAN_BYTES;
        $truncated = false;

        foreach ($files as $file) {
            $found = 0;

            foreach ($this->entriesBackward($file['path'], $budget) as $entry) {
                if ($entry === null) {
                    $truncated = true;
                    break 2;
                }

                if ($from !== null && $entry['date'] < $from) {
                    break;
                }

                if ($to !== null && $entry['date'] > $to) {
                    continue;
                }

                if ($minLevel !== null && (self::LEVELS[strtolower($entry['level'])] ?? 0) < $minLevel) {
                    continue;
                }

                if ($search !== null && ! str_contains(mb_strtolower($entry['message']."\n".implode("\n", $entry['trace'])), $search)) {
                    continue;
                }

                $entries[] = $this->present($entry, $file['name']);

                if (++$found >= $limit) {
                    break;
                }
            }
        }

        usort($entries, fn (array $a, array $b) => strcmp($b['date'], $a['date']));

        return response()->json([
            'files' => array_map(fn (array $f) => [
                'name' => $f['name'],
                'size' => $f['size'],
                'modified_at' => date('Y-m-d H:i:s', $f['mtime']),
            ], $files),
            'truncated' => $truncated,
            'entries' => array_slice($entries, 0, $limit),
        ]);
    }

    /**
     * Fichiers laravel*.log du dossier des journaux, le plus récent d'abord.
     *
     * @return list<array{path: string, name: string, size: int, mtime: int}>
     */
    private function logFiles(): array
    {
        $paths = glob(rtrim((string) config('ai.logs_path'), '/').'/laravel*.log') ?: [];

        $files = array_map(fn (string $path) => [
            'path' => $path,
            'name' => basename($path),
            'size' => (int) filesize($path),
            'mtime' => (int) filemtime($path),
        ], array_filter($paths, 'is_file'));

        usort($files, fn (array $a, array $b) => $b['mtime'] <=> $a['mtime']);

        return array_values($files);
    }

    /**
     * Entrées d'un fichier, de la plus récente à la plus ancienne. Produit null quand le budget de lecture est épuisé.
     *
     * @return Generator<int, array{date: string, environment: string, level: string, message: string, trace: list<string>}|null>
     */
    private function entriesBackward(string $path, int &$budget): Generator
    {
        $continuation = [];

        foreach ($this->linesBackward($path, $budget) as $line) {
            if ($line === null) {
                yield null;

                return;
            }

            if (preg_match(self::HEADER, $line, $m)) {
                yield [
                    'date' => str_replace('T', ' ', $m[1]),
                    'environment' => $m[2],
                    'level' => $m[3],
                    'message' => $m[4],
                    'trace' => array_reverse($continuation),
                ];
                $continuation = [];
            } elseif ($line !== '') {
                $continuation[] = $line;
            }
        }
    }

    /**
     * Lignes d'un fichier en partant de la fin, par blocs. Produit null quand le budget de lecture est épuisé.
     *
     * @return Generator<int, string|null>
     */
    private function linesBackward(string $path, int &$budget): Generator
    {
        $handle = @fopen($path, 'rb');

        if ($handle === false) {
            return;
        }

        try {
            fseek($handle, 0, SEEK_END);
            $position = ftell($handle);
            $rest = '';

            while ($position > 0) {
                if ($budget <= 0) {
                    yield null;

                    return;
                }

                $size = min(self::CHUNK_BYTES, $position);
                $position -= $size;
                $budget -= $size;
                fseek($handle, $position);
                $lines = explode("\n", fread($handle, $size).$rest);
                $rest = array_shift($lines);

                for ($i = count($lines) - 1; $i >= 0; $i--) {
                    yield rtrim($lines[$i], "\r");
                }
            }

            yield rtrim($rest, "\r");
        } finally {
            fclose($handle);
        }
    }

    /**
     * @param  array{date: string, environment: string, level: string, message: string, trace: list<string>}  $entry
     * @return array<string, mixed>
     */
    private function present(array $entry, string $file): array
    {
        return [
            'date' => $entry['date'],
            'level' => strtolower($entry['level']),
            'environment' => $entry['environment'],
            'message' => mb_strimwidth($entry['message'], 0, self::MAX_MESSAGE_CHARS, '…'),
            'trace' => array_slice($entry['trace'], 0, self::MAX_TRACE_LINES),
            'trace_lines' => count($entry['trace']),
            'file' => $file,
        ];
    }

    /** Une date seule (2026-11-09) couvre toute la journée. */
    private function endOfPeriod(string $to): string
    {
        $date = Carbon::parse($to);

        return preg_match('/^\d{4}-\d{2}-\d{2}$/', trim($to))
            ? $date->endOfDay()->format('Y-m-d H:i:s')
            : $date->format('Y-m-d H:i:s');
    }
}
