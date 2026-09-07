<?php

namespace Tests\Unit\Services\Catalogue;

use App\Services\Catalogue\LightAttributeExtractor;
use PHPUnit\Framework\TestCase;

class LightAttributeExtractorTest extends TestCase
{
    public function test_extracts_position_and_normalized_power_source(): void
    {
        $extractor = new LightAttributeExtractor;

        $attributes = $extractor->extract('ECLAIRAGE VELO AV+AR PILE OPTIMIZ FIXATION CINTRE/TIGE DE SELLE LED ALU');

        $this->assertSame('AV+AR', $attributes['position']);
        $this->assertSame('pile', $attributes['power_source']);
    }

    public function test_normalizes_rechargeable_power_source(): void
    {
        $extractor = new LightAttributeExtractor;

        $attributes = $extractor->extract('ECLAIRAGE VELO AV RECHARG.MICRO USBSIGMA AURA 60 FIXATION CINTRE LED');

        $this->assertSame('AV', $attributes['position']);
        $this->assertSame('rechargeable', $attributes['power_source']);
    }

    public function test_does_not_support_accessories_misclassified_in_eclairage(): void
    {
        $extractor = new LightAttributeExtractor;

        $this->assertFalse($extractor->supports('ECLAIRAGE', 'SUPPORT GUIDON POUR ECLAIRAGE VELO'));
        $this->assertFalse($extractor->supports('ECLAIRAGE', 'AUTOCOLLANT/STICKER ECLAIRAGE'));
    }

    public function test_supports_frontal_prefix_variant(): void
    {
        $extractor = new LightAttributeExtractor;

        $this->assertTrue($extractor->supports('ECLAIRAGE', 'ECLAIRAGE FRONTAL SIGMA HEADLED II RECHARG.MICRO USB'));
    }
}
