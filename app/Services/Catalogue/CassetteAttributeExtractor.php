<?php

namespace App\Services\Catalogue;

class CassetteAttributeExtractor implements AttributeExtractor
{
    public function supports(string $rawSubcategory, string $designation): bool
    {
        return strtoupper(trim($rawSubcategory)) === 'ROUE-LIBRES/CASSETTES'
            && str_starts_with(strtoupper(trim($designation)), 'CASSETTE');
    }

    public function extract(string $designation): array
    {
        $attributes = [];

        if (preg_match('/(\d+)\s*V\./i', $designation, $speedMatch)) {
            $attributes['speed_count'] = $speedMatch[1];
        }

        if (preg_match('/V\.\s*(\S+)/i', $designation, $practiceMatch)) {
            $attributes['practice_type'] = $practiceMatch[1];
        }

        if (preg_match('/-\s*(\d+-\d+)\s*DTS/i', $designation, $toothMatch)) {
            $attributes['tooth_range'] = $toothMatch[1];
        }

        return $attributes;
    }

    public function possibleKeys(): array
    {
        return ['speed_count', 'practice_type', 'tooth_range'];
    }
}
