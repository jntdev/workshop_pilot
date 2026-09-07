<?php

namespace App\Services\Catalogue;

interface AttributeExtractor
{
    public function supports(string $rawSubcategory, string $designation): bool;

    /**
     * @return array<string, string>
     */
    public function extract(string $designation): array;

    /**
     * @return array<int, string>
     */
    public function possibleKeys(): array;
}
