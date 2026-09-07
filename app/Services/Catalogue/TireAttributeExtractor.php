<?php

namespace App\Services\Catalogue;

class TireAttributeExtractor implements AttributeExtractor
{
    public function supports(string $rawSubcategory, string $designation): bool
    {
        return strtoupper(trim($rawSubcategory)) === 'PNEUS VELO';
    }

    public function extract(string $designation): array
    {
        $attributes = [];

        $words = preg_split('/\s+/', trim($designation));

        if (isset($words[1])) {
            $attributes['practice_type'] = $words[1];
        }

        if (isset($words[2])) {
            $attributes['size_inches'] = $words[2];
        }

        if (preg_match('/(\d+)-(\d+)/', $designation, $matches)) {
            $attributes['etrto_size'] = $matches[0];
        }

        return $attributes;
    }

    public function possibleKeys(): array
    {
        return ['practice_type', 'size_inches', 'etrto_size'];
    }
}
