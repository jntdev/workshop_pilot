<?php

namespace Tests\Unit\Services\Catalogue;

use App\Services\Catalogue\CassetteAttributeExtractor;
use PHPUnit\Framework\TestCase;

class CassetteAttributeExtractorTest extends TestCase
{
    public function test_extracts_speed_practice_and_tooth_range_from_nominal_designation(): void
    {
        $extractor = new CassetteAttributeExtractor;

        $attributes = $extractor->extract('CASSETTE 10V. VTT SHIMANO DEORE CS-M4100 - 11-42DTS');

        $this->assertSame('10', $attributes['speed_count']);
        $this->assertSame('VTT', $attributes['practice_type']);
        $this->assertSame('11-42', $attributes['tooth_range']);
    }

    public function test_returns_no_tooth_range_when_absent(): void
    {
        $extractor = new CassetteAttributeExtractor;

        $attributes = $extractor->extract('CASSETTE  9V. ROUTE MICHE PRIMATO ADAPT. CAMPA');

        $this->assertArrayNotHasKey('tooth_range', $attributes);
        $this->assertSame('9', $attributes['speed_count']);
        $this->assertSame('ROUTE', $attributes['practice_type']);
    }

    public function test_supports_cassette_et_chaine_prefix(): void
    {
        $extractor = new CassetteAttributeExtractor;

        $this->assertTrue($extractor->supports('ROUE-LIBRES/CASSETTES', 'CASSETTE ET CHAINE  8V. VTT SUNRACE CSM66/CNM84 - 11-32DTS (COMPATIBLE SHIMANO)'));
    }

    public function test_does_not_support_roue_libre_or_corps_cassette(): void
    {
        $extractor = new CassetteAttributeExtractor;

        $this->assertFalse($extractor->supports('ROUE-LIBRES/CASSETTES', 'ROUE LIBRE 18 DTS MONOVITESSE BRONZE'));
        $this->assertFalse($extractor->supports('ROUE-LIBRES/CASSETTES', 'CORPS CASSETTE SHIMANO RM30 7V POUR AXE QR'));
    }
}
