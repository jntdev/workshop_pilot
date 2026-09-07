<?php

namespace App\Services\Catalogue;

class LightAttributeExtractor implements AttributeExtractor
{
    private const POWER_SOURCE_MAP = [
        'DYNAMO' => 'dynamo',
        'PILE' => 'pile',
        'RECHARG' => 'rechargeable',
    ];

    public function supports(string $rawSubcategory, string $designation): bool
    {
        return strtoupper(trim($rawSubcategory)) === 'ECLAIRAGE'
            && preg_match('/^ECLAIRAGE (VELO|FRONTAL)/i', trim($designation)) === 1;
    }

    public function extract(string $designation): array
    {
        $attributes = [];

        if (preg_match('/\b(AV\+AR|AV\/PROJECTEUR|AV|AR)\b/i', $designation, $positionMatch)) {
            $attributes['position'] = strtoupper($positionMatch[1]);
        }

        if (preg_match('/\b(DYNAMO|PILE|RECHARG)\.?/i', $designation, $powerMatch)) {
            $attributes['power_source'] = self::POWER_SOURCE_MAP[strtoupper($powerMatch[1])];
        }

        return $attributes;
    }

    public function possibleKeys(): array
    {
        return ['position', 'power_source'];
    }
}
