<?php

namespace App\Services\Catalogue;

class InnerTubeAttributeExtractor implements AttributeExtractor
{
    public function supports(string $rawSubcategory, string $designation): bool
    {
        return strtoupper(trim($rawSubcategory)) === 'CHAMBRES VELO';
    }

    public function extract(string $designation): array
    {
        $attributes = [];

        if (preg_match('/CHAMBRE A AIR (?:A )?VELO\s+(.+?)\s*\(/i', $designation, $sizeMatch)) {
            $attributes['size_inches'] = trim($sizeMatch[1]);
        }

        if (preg_match('/(\d+)-(\d+)/', $designation, $etrtoMatch)) {
            $attributes['etrto_size'] = $etrtoMatch[0];
        }

        if (preg_match('/\b(VS|VP)\b/', $designation, $valveMatch)) {
            $attributes['valve_type'] = $valveMatch[1];
        }

        return $attributes;
    }

    public function possibleKeys(): array
    {
        return ['size_inches', 'etrto_size', 'valve_type'];
    }
}
