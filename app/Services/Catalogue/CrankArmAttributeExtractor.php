<?php

namespace App\Services\Catalogue;

class CrankArmAttributeExtractor implements AttributeExtractor
{
    public function supports(string $rawSubcategory, string $designation): bool
    {
        return strtoupper(trim($rawSubcategory)) === 'MANIVELLES'
            && str_starts_with(strtoupper(trim($designation)), 'MANIVELLE');
    }

    public function extract(string $designation): array
    {
        $attributes = [];

        if (preg_match('/\bGAUCHE\b.*\bDROITE\b|\bDROITE\b.*\bGAUCHE\b/i', $designation)
            || preg_match('/\(PAIRE\)|\(PR\)/i', $designation)) {
            $attributes['side'] = 'PAIRE';
        } elseif (preg_match('/\bGAUCHE\b/i', $designation)) {
            $attributes['side'] = 'GAUCHE';
        } elseif (preg_match('/\bDROITE\b/i', $designation)) {
            $attributes['side'] = 'DROITE';
        }

        if (preg_match('/\bE BIKE\/VAE\b/i', $designation)) {
            $attributes['practice_type'] = 'E BIKE/VAE';
        } else {
            $words = preg_split('/\s+/', trim($designation));
            $secondWord = strtoupper($words[1] ?? '');
            $practiceType = in_array($secondWord, ['GAUCHE', 'DROITE'], true)
                ? ($words[2] ?? null)
                : ($words[1] ?? null);

            if ($practiceType !== null) {
                $attributes['practice_type'] = $practiceType;
            }
        }

        if (preg_match('/L(\d+)\b/i', $designation, $lengthMatch)) {
            $attributes['crank_length_mm'] = $lengthMatch[1];
        } elseif (preg_match('/(\d+)\s*MM/i', $designation, $lengthMatch)) {
            $attributes['crank_length_mm'] = $lengthMatch[1];
        }

        return $attributes;
    }

    public function possibleKeys(): array
    {
        return ['side', 'practice_type', 'crank_length_mm'];
    }
}
