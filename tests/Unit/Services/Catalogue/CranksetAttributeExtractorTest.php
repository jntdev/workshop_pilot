<?php

namespace Tests\Unit\Services\Catalogue;

use App\Services\Catalogue\CranksetAttributeExtractor;
use PHPUnit\Framework\TestCase;

class CranksetAttributeExtractorTest extends TestCase
{
    public function test_extracts_practice_tooth_range_length_and_speed_from_nominal_designation(): void
    {
        $extractor = new CranksetAttributeExtractor;

        $attributes = $extractor->extract('PEDALIER ROUTE TRIPLE 48-38-28D L170 STRONG IMPACT ARGENT 9/10V');

        $this->assertSame('ROUTE', $attributes['practice_type']);
        $this->assertSame('48-38-28', $attributes['tooth_range']);
        $this->assertSame('170', $attributes['crank_length_mm']);
        $this->assertSame('10', $attributes['speed_count']);
    }

    public function test_finds_practice_type_after_chainring_count_word(): void
    {
        $extractor = new CranksetAttributeExtractor;

        $attributes = $extractor->extract('PEDALIER MONO CITY 44D L170 STRONG 55S ALU ARGENT (2.38 - 3/32")');

        $this->assertSame('CITY', $attributes['practice_type']);
        $this->assertSame('44', $attributes['tooth_range']);
    }

    public function test_returns_empty_array_for_designation_out_of_pattern(): void
    {
        $extractor = new CranksetAttributeExtractor;

        $this->assertFalse($extractor->supports('PEDALIERS', 'VIS SERRAGE MANIVELLE SUR BOITIER DE PEDALIER CARRE D8X100 HEXAGONALE A EMBASE (X1)'));
    }
}
