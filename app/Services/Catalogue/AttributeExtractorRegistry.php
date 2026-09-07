<?php

namespace App\Services\Catalogue;

class AttributeExtractorRegistry
{
    /**
     * @var array<int, AttributeExtractor>
     */
    private array $extractors;

    public function __construct()
    {
        $this->extractors = [
            new TireAttributeExtractor,
            new InnerTubeAttributeExtractor,
            new ChainringAttributeExtractor,
            new CranksetAttributeExtractor,
            new CrankArmAttributeExtractor,
            new CassetteAttributeExtractor,
            new FreewheelAttributeExtractor,
            new CassetteBodyAttributeExtractor,
            new LightAttributeExtractor,
        ];
    }

    public function findFor(string $rawSubcategory, string $designation): ?AttributeExtractor
    {
        foreach ($this->extractors as $extractor) {
            if ($extractor->supports($rawSubcategory, $designation)) {
                return $extractor;
            }
        }

        return null;
    }
}
