<?php

namespace Tests\Unit\Services\Catalogue;

use App\Services\Catalogue\InnerTubeAttributeExtractor;
use PHPUnit\Framework\TestCase;

class InnerTubeAttributeExtractorTest extends TestCase
{
    public function test_extracts_size_etrto_and_valve_from_nominal_designation(): void
    {
        $extractor = new InnerTubeAttributeExtractor;

        $attributes = $extractor->extract('CHAMBRE A AIR VELO 20" X 1 3/8 (37-406) VS OPTIMIZ');

        $this->assertSame('20" X 1 3/8', $attributes['size_inches']);
        $this->assertSame('37-406', $attributes['etrto_size']);
        $this->assertSame('VS', $attributes['valve_type']);
    }

    public function test_returns_no_etrto_key_when_only_slash_notation_present(): void
    {
        $extractor = new InnerTubeAttributeExtractor;

        $attributes = $extractor->extract('CHAMBRE A AIR VELO 20" X 2.00 (54/152) VS');

        $this->assertArrayNotHasKey('etrto_size', $attributes);
    }

    public function test_supports_only_chambres_velo_subcategory(): void
    {
        $extractor = new InnerTubeAttributeExtractor;

        $this->assertTrue($extractor->supports('CHAMBRES VELO', 'CHAMBRE A AIR VELO 700C'));
        $this->assertFalse($extractor->supports('PNEUS VELO', 'PNEU ROUTE 700X28C'));
    }
}
