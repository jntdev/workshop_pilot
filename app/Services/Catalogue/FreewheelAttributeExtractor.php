<?php

namespace App\Services\Catalogue;

class FreewheelAttributeExtractor implements AttributeExtractor
{
    public function supports(string $rawSubcategory, string $designation): bool
    {
        return strtoupper(trim($rawSubcategory)) === 'ROUE-LIBRES/CASSETTES'
            && str_starts_with(strtoupper(trim($designation)), 'ROUE LIBRE');
    }

    public function extract(string $designation): array
    {
        $attributes = [];

        if (preg_match('/(\d+)\s*V\./i', $designation, $speedMatch)) {
            $attributes['speed_count'] = $speedMatch[1];

            if (preg_match('/(\d+-\d+)\s*DTS/i', $designation, $toothMatch)) {
                $attributes['tooth_range'] = $toothMatch[1];
            }

            return $attributes;
        }

        if (preg_match('/^ROUE LIBRE\s+(\d+)\s*DTS/i', $designation, $countMatch)) {
            $attributes['tooth_count'] = $countMatch[1];
        }

        return $attributes;
    }

    public function possibleKeys(): array
    {
        return ['speed_count', 'tooth_range', 'tooth_count'];
    }
}
