<?php

namespace Tests\Unit\Services\Catalogue;

use App\Services\Catalogue\CassetteBodyAttributeExtractor;
use PHPUnit\Framework\TestCase;

class CassetteBodyAttributeExtractorTest extends TestCase
{
    public function test_extracts_speed_compat_and_qr_axle_from_nominal_designation(): void
    {
        $extractor = new CassetteBodyAttributeExtractor;

        $attributes = $extractor->extract('CORPS CASSETTE SHIMANO XT M770/775/776 9/10V POUR AXE QR (Y3CZ98050)');

        $this->assertSame('9/10', $attributes['speed_compat']);
        $this->assertSame('QR', $attributes['axle_type']);
    }

    public function test_supports_corps_de_cassette_variant(): void
    {
        $extractor = new CassetteBodyAttributeExtractor;

        $this->assertTrue($extractor->supports('ROUE-LIBRES/CASSETTES', 'CORPS DE CASSETTE SHIMANO ULTEGRA WH-R8170  12V POUR AXE TRAVERSANT  (Y3EX98040)'));
    }

    public function test_extracts_traversant_axle_type(): void
    {
        $extractor = new CassetteBodyAttributeExtractor;

        $attributes = $extractor->extract('CORPS CASSETTE SHIMANO RS 370  11V POUR AXE TRAVERSANT (Y0FH98010)');

        $this->assertSame('TRAVERSANT', $attributes['axle_type']);
    }
}
