<?php

namespace App\Services\Catalogue;

class CranksetAttributeExtractor implements AttributeExtractor
{
    private const CHAINRING_COUNT_WORDS = ['MONO', 'DOUBLE', 'TRIPLE', 'COMPACT'];

    public function supports(string $rawSubcategory, string $designation): bool
    {
        return strtoupper(trim($rawSubcategory)) === 'PEDALIERS'
            && str_starts_with(strtoupper(trim($designation)), 'PEDALIER');
    }

    public function extract(string $designation): array
    {
        $attributes = [];

        $words = preg_split('/\s+/', trim($designation));
        $secondWord = strtoupper($words[1] ?? '');

        $practiceType = in_array($secondWord, self::CHAINRING_COUNT_WORDS, true)
            ? ($words[2] ?? null)
            : ($words[1] ?? null);

        if ($practiceType !== null) {
            $attributes['practice_type'] = $practiceType;
        }

        if (preg_match('/([\d\-]+)D\b/i', $designation, $toothMatch)) {
            $attributes['tooth_range'] = $toothMatch[1];
        }

        if (preg_match('/L(\d+(?:\.\d+)?)/i', $designation, $lengthMatch)) {
            $attributes['crank_length_mm'] = $lengthMatch[1];
        }

        if (preg_match('/(\d+)V\.?(?:\s|$)/i', $designation, $speedMatch)) {
            $attributes['speed_count'] = $speedMatch[1];
        }

        return $attributes;
    }

    public function possibleKeys(): array
    {
        return ['practice_type', 'tooth_range', 'crank_length_mm', 'speed_count'];
    }
}
