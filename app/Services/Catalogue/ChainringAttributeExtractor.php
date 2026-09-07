<?php

namespace App\Services\Catalogue;

class ChainringAttributeExtractor implements AttributeExtractor
{
    public function supports(string $rawSubcategory, string $designation): bool
    {
        return strtoupper(trim($rawSubcategory)) === 'PLATEAUX'
            && str_starts_with(strtoupper(trim($designation)), 'PLATEAU');
    }

    public function extract(string $designation): array
    {
        $attributes = [];

        $words = preg_split('/\s+/', trim($designation));
        if (isset($words[1])) {
            $attributes['practice_type'] = $words[1];
        }

        if (preg_match('/DIAM\s+(\d+)/i', $designation, $diamMatch)) {
            $attributes['chainring_diameter_mm'] = $diamMatch[1];
        }

        if (preg_match('/(\d+)\s*DTS/i', $designation, $toothMatch)) {
            $attributes['tooth_count'] = $toothMatch[1];
        }

        if (preg_match('/(\d+)V\.?(?:\s|$)/i', $designation, $speedMatch)) {
            $attributes['speed_count'] = $speedMatch[1];
        }

        return $attributes;
    }

    public function possibleKeys(): array
    {
        return ['practice_type', 'chainring_diameter_mm', 'tooth_count', 'speed_count'];
    }
}
