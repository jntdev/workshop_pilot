<?php

namespace App\Services\Quotes;

class QuoteMerger
{
    /**
     * Champs scalaires du devis comparés pour détecter une divergence.
     *
     * @var array<int, string>
     */
    private const COMPARABLE_FIELDS = [
        'client_id',
        'client_prenom',
        'client_nom',
        'client_email',
        'client_telephone',
        'client_adresse',
        'client_origine_contact',
        'client_commentaires',
        'client_avantage_type',
        'client_avantage_valeur',
        'client_avantage_expiration',
        'bike_description',
        'reception_comment',
        'remarks',
        'email_note',
        'valid_until',
        'discount_type',
        'discount_value',
        'actual_time_minutes',
    ];

    public function __construct(private QuoteLineMerger $lineMerger) {}

    /**
     * Fusionne un devis à trois voies : base (état d'ouverture), mine (état soumis par
     * cet onglet) et theirs (état actuel en base de données).
     *
     * @param  array<string, mixed>  $base
     * @param  array<string, mixed>  $mine
     * @param  array<string, mixed>  $theirs
     * @param  array{fields?: array<string, mixed>, lines?: array<string, mixed>}  $resolvedConflicts
     */
    public function merge(array $base, array $mine, array $theirs, array $resolvedConflicts = []): QuoteMergeResult
    {
        $resolvedFields = $resolvedConflicts['fields'] ?? [];
        $resolvedLines = $resolvedConflicts['lines'] ?? [];

        [$fields, $fieldConflicts] = $this->mergeFields($base, $mine, $theirs, $resolvedFields);

        $lineMergeResult = $this->lineMerger->merge(
            $base['lines'] ?? [],
            $mine['lines'] ?? [],
            $theirs['lines'] ?? [],
        );

        [$lines, $lineConflicts] = $this->applyResolvedLineConflicts($lineMergeResult, $resolvedLines);

        return new QuoteMergeResult(
            fields: $fields,
            lines: $lines,
            fieldConflicts: $fieldConflicts,
            lineConflicts: $lineConflicts,
        );
    }

    /**
     * @param  array<string, mixed>  $base
     * @param  array<string, mixed>  $mine
     * @param  array<string, mixed>  $theirs
     * @param  array<string, mixed>  $resolvedFields
     * @return array{0: array<string, mixed>, 1: array<int, array<string, mixed>>}
     */
    private function mergeFields(array $base, array $mine, array $theirs, array $resolvedFields): array
    {
        $fields = [];
        $conflicts = [];

        foreach (self::COMPARABLE_FIELDS as $field) {
            $baseValue = $base[$field] ?? null;
            $mineValue = $mine[$field] ?? null;
            $theirsValue = $theirs[$field] ?? null;

            $mineChanged = $this->valuesDiffer($baseValue, $mineValue);
            $theirsChanged = $this->valuesDiffer($baseValue, $theirsValue);

            if (! $mineChanged && ! $theirsChanged) {
                $fields[$field] = $baseValue;

                continue;
            }

            if ($mineChanged && ! $theirsChanged) {
                $fields[$field] = $mineValue;

                continue;
            }

            if (! $mineChanged && $theirsChanged) {
                $fields[$field] = $theirsValue;

                continue;
            }

            if (! $this->valuesDiffer($mineValue, $theirsValue)) {
                $fields[$field] = $mineValue;

                continue;
            }

            if (array_key_exists($field, $resolvedFields)) {
                $fields[$field] = $resolvedFields[$field];

                continue;
            }

            $conflicts[] = [
                'path' => $field,
                'base' => $baseValue,
                'mine' => $mineValue,
                'theirs' => $theirsValue,
            ];
        }

        return [$fields, $conflicts];
    }

    /**
     * @param  array{lines: array<int, array<string, mixed>>, conflicts: array<int, array<string, mixed>>}  $lineMergeResult
     * @param  array<string, mixed>  $resolvedLines
     * @return array{0: array<int, array<string, mixed>>, 1: array<int, array<string, mixed>>}
     */
    private function applyResolvedLineConflicts(array $lineMergeResult, array $resolvedLines): array
    {
        $lines = $lineMergeResult['lines'];
        $remainingConflicts = [];

        foreach ($lineMergeResult['conflicts'] as $conflict) {
            $lineKey = $conflict['line_key'];

            if (! array_key_exists($lineKey, $resolvedLines)) {
                $remainingConflicts[] = $conflict;

                continue;
            }

            $resolvedLine = $resolvedLines[$lineKey];

            if ($resolvedLine !== null) {
                $lines[] = $resolvedLine;
            }
        }

        foreach ($lines as $index => &$line) {
            $line['position'] = $index;
        }

        return [$lines, $remainingConflicts];
    }

    private function valuesDiffer(mixed $a, mixed $b): bool
    {
        return (string) ($a ?? '') !== (string) ($b ?? '');
    }
}
