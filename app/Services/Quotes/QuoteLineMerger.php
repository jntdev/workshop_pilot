<?php

namespace App\Services\Quotes;

class QuoteLineMerger
{
    /**
     * Champs comparés pour détecter une divergence de contenu sur une ligne.
     *
     * @var array<int, string>
     */
    private const COMPARABLE_FIELDS = [
        'title',
        'reference',
        'quantity',
        'purchase_price_ht',
        'sale_price_ht',
        'sale_price_ttc',
        'margin_amount_ht',
        'margin_rate',
        'tva_rate',
        'estimated_time_minutes',
        'needs_order',
        'ordered_at',
        'received_at',
        'article_id',
    ];

    /**
     * Fusionne les lignes d'un devis en comparant base (état d'ouverture), mine (état soumis)
     * et theirs (état actuel en base) selon un merge à trois voies.
     *
     * @param  array<int, array<string, mixed>>  $base
     * @param  array<int, array<string, mixed>>  $mine
     * @param  array<int, array<string, mixed>>  $theirs
     * @return array{lines: array<int, array<string, mixed>>, conflicts: array<int, array<string, mixed>>}
     */
    public function merge(array $base, array $mine, array $theirs): array
    {
        // Seules les lignes avec une identité stable (id ou client_key) peuvent être
        // comparées entre base/mine/theirs. Une ligne de mine sans id ni client_key ne
        // peut provenir que d'un payload legacy sans base explicite : elle est toujours
        // traitée comme un ajout, jamais rapprochée d'une ligne de base/theirs.
        $identifiableMine = array_filter($mine, fn (array $line) => $this->hasIdentity($line));
        $unidentifiableMine = array_filter($mine, fn (array $line) => ! $this->hasIdentity($line));

        $baseByKey = $this->indexByKey($base);
        $mineByKey = $this->indexByKey($identifiableMine);
        $theirsByKey = $this->indexByKey($theirs);

        $allKeys = array_unique(array_merge(
            array_keys($baseByKey),
            array_keys($mineByKey),
            array_keys($theirsByKey),
        ));

        $resultLines = [];
        $conflicts = [];

        foreach ($allKeys as $key) {
            $baseLine = $baseByKey[$key] ?? null;
            $mineLine = $mineByKey[$key] ?? null;
            $theirsLine = $theirsByKey[$key] ?? null;

            $outcome = $this->resolveLine($key, $baseLine, $mineLine, $theirsLine);

            if ($outcome['conflict'] !== null) {
                $conflicts[] = $outcome['conflict'];

                continue;
            }

            if ($outcome['line'] !== null) {
                $resultLines[] = $outcome['line'];
            }
        }

        array_push($resultLines, ...array_values($unidentifiableMine));

        return [
            'lines' => $this->reorder($resultLines, $mine, $theirs),
            'conflicts' => $conflicts,
        ];
    }

    /**
     * @param  array<string, mixed>  $line
     */
    private function hasIdentity(array $line): bool
    {
        return ! empty($line['id']) || ! empty($line['client_key']);
    }

    /**
     * @param  ?array<string, mixed>  $base
     * @param  ?array<string, mixed>  $mine
     * @param  ?array<string, mixed>  $theirs
     * @return array{line: ?array<string, mixed>, conflict: ?array<string, mixed>}
     */
    private function resolveLine(string $key, ?array $base, ?array $mine, ?array $theirs): array
    {
        // Absente de base : ajout(s) uniquement, jamais de conflit (les client_key sont uniques par onglet).
        if ($base === null) {
            return ['line' => $mine ?? $theirs, 'conflict' => null];
        }

        // Présente à l'ouverture, supprimée par moi.
        if ($mine === null) {
            if ($theirs === null || ! $this->lineChanged($base, $theirs)) {
                return ['line' => null, 'conflict' => null];
            }

            return [
                'line' => null,
                'conflict' => [
                    'type' => 'delete_vs_update',
                    'line_key' => $key,
                    'base' => $base,
                    'mine' => null,
                    'theirs' => $theirs,
                ],
            ];
        }

        // Présente à l'ouverture, supprimée par l'autre onglet.
        if ($theirs === null) {
            if (! $this->lineChanged($base, $mine)) {
                return ['line' => null, 'conflict' => null];
            }

            return [
                'line' => null,
                'conflict' => [
                    'type' => 'update_vs_delete',
                    'line_key' => $key,
                    'base' => $base,
                    'mine' => $mine,
                    'theirs' => null,
                ],
            ];
        }

        // Présente dans les trois : comparaison champ par champ.
        $mineChanged = $this->lineChanged($base, $mine);
        $theirsChanged = $this->lineChanged($base, $theirs);

        if (! $mineChanged && ! $theirsChanged) {
            return ['line' => $base, 'conflict' => null];
        }

        if ($mineChanged && ! $theirsChanged) {
            return ['line' => $mine, 'conflict' => null];
        }

        if (! $mineChanged && $theirsChanged) {
            return ['line' => $theirs, 'conflict' => null];
        }

        // Les deux ont changé.
        if ($this->linesEqual($mine, $theirs)) {
            return ['line' => $mine, 'conflict' => null];
        }

        return [
            'line' => null,
            'conflict' => [
                'type' => 'line_conflict',
                'line_key' => $key,
                'base' => $base,
                'mine' => $mine,
                'theirs' => $theirs,
            ],
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $lines
     * @return array<string, array<string, mixed>>
     */
    private function indexByKey(array $lines): array
    {
        $indexed = [];

        foreach ($lines as $line) {
            $key = $this->keyFor($line);
            $indexed[$key] = $line;
        }

        return $indexed;
    }

    /**
     * @param  array<string, mixed>  $line
     */
    private function keyFor(array $line): string
    {
        if (! empty($line['id'])) {
            return 'id:'.$line['id'];
        }

        return 'client_key:'.($line['client_key'] ?? '');
    }

    /**
     * @param  array<string, mixed>  $a
     * @param  array<string, mixed>  $b
     */
    private function lineChanged(array $a, array $b): bool
    {
        return ! $this->linesEqual($a, $b);
    }

    /**
     * @param  array<string, mixed>  $a
     * @param  array<string, mixed>  $b
     */
    private function linesEqual(array $a, array $b): bool
    {
        foreach (self::COMPARABLE_FIELDS as $field) {
            $valueA = $a[$field] ?? null;
            $valueB = $b[$field] ?? null;

            if ((string) $valueA !== (string) $valueB) {
                return false;
            }
        }

        return true;
    }

    /**
     * Recalcule l'ordre final : respecte l'ordre de mine pour les lignes qu'il connaît,
     * puis insère les lignes theirs-only à la position qu'elles occupaient dans theirs.
     * Les lignes sans identité stable (id/client_key) ne peuvent pas être retrouvées par
     * clé et sont ajoutées à la fin, dans leur ordre d'origine.
     *
     * @param  array<int, array<string, mixed>>  $resultLines
     * @param  array<int, array<string, mixed>>  $mine
     * @param  array<int, array<string, mixed>>  $theirs
     * @return array<int, array<string, mixed>>
     */
    private function reorder(array $resultLines, array $mine, array $theirs): array
    {
        $identifiableResultLines = array_filter($resultLines, fn (array $line) => $this->hasIdentity($line));
        $unidentifiableResultLines = array_filter($resultLines, fn (array $line) => ! $this->hasIdentity($line));

        $resultByKey = [];
        foreach ($identifiableResultLines as $line) {
            $resultByKey[$this->keyFor($line)] = $line;
        }

        $ordered = [];
        $placed = [];

        foreach ($mine as $line) {
            if (! $this->hasIdentity($line)) {
                continue;
            }

            $key = $this->keyFor($line);
            if (isset($resultByKey[$key]) && ! isset($placed[$key])) {
                $ordered[] = $resultByKey[$key];
                $placed[$key] = true;
            }
        }

        foreach ($theirs as $line) {
            $key = $this->keyFor($line);
            if (isset($resultByKey[$key]) && ! isset($placed[$key])) {
                $ordered[] = $resultByKey[$key];
                $placed[$key] = true;
            }
        }

        foreach ($identifiableResultLines as $line) {
            $key = $this->keyFor($line);
            if (! isset($placed[$key])) {
                $ordered[] = $line;
                $placed[$key] = true;
            }
        }

        array_push($ordered, ...array_values($unidentifiableResultLines));

        foreach ($ordered as $index => &$line) {
            $line['position'] = $index;
        }

        return $ordered;
    }
}
