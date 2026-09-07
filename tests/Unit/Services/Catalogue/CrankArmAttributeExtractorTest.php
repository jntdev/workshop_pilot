<?php

namespace Tests\Unit\Services\Catalogue;

use App\Services\Catalogue\CrankArmAttributeExtractor;
use PHPUnit\Framework\TestCase;

class CrankArmAttributeExtractorTest extends TestCase
{
    public function test_extracts_side_practice_and_length_from_nominal_designation(): void
    {
        $extractor = new CrankArmAttributeExtractor;

        $attributes = $extractor->extract('MANIVELLE GAUCHE VTT L170 ACIER PLASTIFIE NOIR');

        $this->assertSame('GAUCHE', $attributes['side']);
        $this->assertSame('VTT', $attributes['practice_type']);
        $this->assertSame('170', $attributes['crank_length_mm']);
    }

    public function test_detects_paire_from_parenthesis_marker(): void
    {
        $extractor = new CrankArmAttributeExtractor;

        $attributes = $extractor->extract('MANIVELLE VAE SHIMANO STEPS E5000 170MM (PAIRE)');

        $this->assertSame('PAIRE', $attributes['side']);
        $this->assertSame('170', $attributes['crank_length_mm']);
    }

    public function test_returns_empty_array_for_designation_out_of_pattern(): void
    {
        $extractor = new CrankArmAttributeExtractor;

        $this->assertFalse($extractor->supports('MANIVELLES', 'VIS SERRAGE MANIVELLE SUR BOITIER DE PEDALIER ISIS/OCTALINK D15X100 (X1)'));
    }
}
