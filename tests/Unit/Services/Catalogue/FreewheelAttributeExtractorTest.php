<?php

namespace Tests\Unit\Services\Catalogue;

use App\Services\Catalogue\FreewheelAttributeExtractor;
use PHPUnit\Framework\TestCase;

class FreewheelAttributeExtractorTest extends TestCase
{
    public function test_extracts_speed_and_tooth_range_for_multi_speed_form(): void
    {
        $extractor = new FreewheelAttributeExtractor;

        $attributes = $extractor->extract('ROUE LIBRE  7 V. SHIMANO TZ500 14-28DTS');

        $this->assertSame('7', $attributes['speed_count']);
        $this->assertSame('14-28', $attributes['tooth_range']);
    }

    public function test_extracts_tooth_count_for_single_speed_form(): void
    {
        $extractor = new FreewheelAttributeExtractor;

        $attributes = $extractor->extract('ROUE LIBRE 18 DTS MONOVITESSE BRONZE');

        $this->assertSame('18', $attributes['tooth_count']);
        $this->assertArrayNotHasKey('tooth_range', $attributes);
        $this->assertArrayNotHasKey('speed_count', $attributes);
    }

    public function test_does_not_support_cassette_or_corps_cassette_lines(): void
    {
        $extractor = new FreewheelAttributeExtractor;

        $this->assertFalse($extractor->supports('ROUE-LIBRES/CASSETTES', 'CASSETTE 10V. VTT SHIMANO DEORE CS-M4100 - 11-42DTS'));
        $this->assertFalse($extractor->supports('ROUE-LIBRES/CASSETTES', 'CORPS CASSETTE SHIMANO RM30 7V POUR AXE QR'));
    }
}
