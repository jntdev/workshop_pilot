<?php

namespace App\Services\Catalogue;

class CassetteBodyAttributeExtractor implements AttributeExtractor
{
    public function supports(string $rawSubcategory, string $designation): bool
    {
        $upperDesignation = strtoupper(trim($designation));

        return strtoupper(trim($rawSubcategory)) === 'ROUE-LIBRES/CASSETTES'
            && (str_starts_with($upperDesignation, 'CORPS CASSETTE')
                || str_starts_with($upperDesignation, 'CORPS DE CASSETTE'));
    }

    public function extract(string $designation): array
    {
        $attributes = [];

        if (preg_match('/([\d\/]+)V\s+POUR AXE/i', $designation, $speedMatch)) {
            $attributes['speed_compat'] = $speedMatch[1];
        }

        if (preg_match('/POUR AXE\s+(QR|TRAVERSANT)/i', $designation, $axleMatch)) {
            $attributes['axle_type'] = strtoupper($axleMatch[1]);
        }

        return $attributes;
    }

    public function possibleKeys(): array
    {
        return ['speed_compat', 'axle_type'];
    }
}
