<?php

namespace Tests\Unit\Services\Catalogue;

use App\Services\Catalogue\TireAttributeExtractor;
use PHPUnit\Framework\TestCase;

class TireAttributeExtractorTest extends TestCase
{
    public function test_extracts_practice_size_and_etrto_from_nominal_designation(): void
    {
        $extractor = new TireAttributeExtractor;

        $attributes = $extractor->extract('PNEU ROUTE 700X28C TR MICHELIN DYNAMIC CLASSIC TT NOIR/BEIGE (28-622)');

        $this->assertSame('ROUTE', $attributes['practice_type']);
        $this->assertSame('700X28C', $attributes['size_inches']);
        $this->assertSame('28-622', $attributes['etrto_size']);
    }

    public function test_returns_no_etrto_key_for_truncated_designation(): void
    {
        $extractor = new TireAttributeExtractor;

        $attributes = $extractor->extract('PNEU VTC/URBAIN 700X32C - 28X1.25 TR DELI S-192 BLUE WAY ANTICREVAISON 2.5MM TT NOIR  (32-');

        $this->assertArrayNotHasKey('etrto_size', $attributes);
        $this->assertSame('VTC/URBAIN', $attributes['practice_type']);
    }

    public function test_supports_only_pneus_velo_subcategory(): void
    {
        $extractor = new TireAttributeExtractor;

        $this->assertTrue($extractor->supports('PNEUS VELO', 'PNEU ROUTE 700X28C'));
        $this->assertFalse($extractor->supports('CHAMBRES VELO', 'CHAMBRE A AIR VELO 700C'));
    }
}
