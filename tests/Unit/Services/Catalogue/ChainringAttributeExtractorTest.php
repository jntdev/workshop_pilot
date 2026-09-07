<?php

namespace Tests\Unit\Services\Catalogue;

use App\Services\Catalogue\ChainringAttributeExtractor;
use PHPUnit\Framework\TestCase;

class ChainringAttributeExtractorTest extends TestCase
{
    public function test_extracts_practice_diameter_tooth_count_and_speed_from_nominal_designation(): void
    {
        $extractor = new ChainringAttributeExtractor;

        $attributes = $extractor->extract('PLATEAU ROUTE DIAM 130 INTER 38DTS ARGENT ALIZE (COMP.SHIMANO) TA 8/9/10/11V. 5 BRANCHES');

        $this->assertSame('ROUTE', $attributes['practice_type']);
        $this->assertSame('130', $attributes['chainring_diameter_mm']);
        $this->assertSame('38', $attributes['tooth_count']);
        $this->assertSame('11', $attributes['speed_count']);
    }

    public function test_returns_empty_array_for_misclassified_designation(): void
    {
        $extractor = new ChainringAttributeExtractor;

        $this->assertFalse($extractor->supports('PLATEAUX', 'INTRAVIS FIX. DOUBLE PLATEAU ROUTE SHIMANO ULTEGRA R8000 M8X10.1 VIS ET CHEMINEE (X4)'));
    }
}
