<?php

namespace Tests\Unit\Services\Agenda;

use App\Services\Agenda\OpeningHours\OpeningHours;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OpeningHoursTest extends TestCase
{
    private OpeningHours $hours;

    protected function setUp(): void
    {
        parent::setUp();
        $this->hours = new OpeningHours;
    }

    #[Test]
    public function it_considers_tuesday_to_saturday_as_open_days(): void
    {
        // 2026-09-22 = mardi
        foreach (range(22, 26) as $day) {
            $date = CarbonImmutable::parse("2026-09-{$day}");
            $this->assertTrue($this->hours->isOpenDay($date), "{$date->toDateString()} devrait être ouvert");
        }
    }

    #[Test]
    public function it_considers_sunday_and_monday_as_closed_days(): void
    {
        // 2026-09-20 = dimanche, 2026-09-21 = lundi
        $this->assertFalse($this->hours->isOpenDay(CarbonImmutable::parse('2026-09-20')));
        $this->assertFalse($this->hours->isOpenDay(CarbonImmutable::parse('2026-09-21')));
    }

    #[Test]
    public function next_open_day_from_monday_is_tuesday(): void
    {
        $result = $this->hours->nextOpenDay(CarbonImmutable::parse('2026-09-21'));

        $this->assertSame('2026-09-22', $result->toDateString());
    }

    #[Test]
    public function next_open_day_from_saturday_skips_sunday_and_monday(): void
    {
        $result = $this->hours->nextOpenDay(CarbonImmutable::parse('2026-09-26'));

        $this->assertSame('2026-09-29', $result->toDateString());
    }

    #[Test]
    public function next_open_day_from_sunday_is_tuesday(): void
    {
        $result = $this->hours->nextOpenDay(CarbonImmutable::parse('2026-09-20'));

        $this->assertSame('2026-09-22', $result->toDateString());
    }

    #[Test]
    public function next_open_day_from_midweek_is_the_following_day(): void
    {
        $result = $this->hours->nextOpenDay(CarbonImmutable::parse('2026-09-22'));

        $this->assertSame('2026-09-23', $result->toDateString());
    }

    #[Test]
    public function next_slot_before_morning_start_snaps_to_nine_am_same_day(): void
    {
        $result = $this->hours->nextSlotAtOrAfter(CarbonImmutable::parse('2026-09-22 07:30'));

        $this->assertSame('2026-09-22 09:00', $result->format('Y-m-d H:i'));
    }

    #[Test]
    public function next_slot_within_lunch_break_snaps_to_two_pm_same_day(): void
    {
        $result = $this->hours->nextSlotAtOrAfter(CarbonImmutable::parse('2026-09-22 12:30'));

        $this->assertSame('2026-09-22 14:00', $result->format('Y-m-d H:i'));
    }

    #[Test]
    public function next_slot_at_exactly_noon_snaps_to_two_pm_same_day(): void
    {
        $result = $this->hours->nextSlotAtOrAfter(CarbonImmutable::parse('2026-09-22 12:00'));

        $this->assertSame('2026-09-22 14:00', $result->format('Y-m-d H:i'));
    }

    #[Test]
    public function next_slot_at_or_after_closing_snaps_to_next_open_day(): void
    {
        $result = $this->hours->nextSlotAtOrAfter(CarbonImmutable::parse('2026-09-22 18:30'));

        $this->assertSame('2026-09-23 09:00', $result->format('Y-m-d H:i'));
    }

    #[Test]
    public function next_slot_on_a_closed_day_snaps_to_next_open_day(): void
    {
        $result = $this->hours->nextSlotAtOrAfter(CarbonImmutable::parse('2026-09-21 10:00'));

        $this->assertSame('2026-09-22 09:00', $result->format('Y-m-d H:i'));
    }

    #[Test]
    public function next_slot_already_valid_is_unchanged(): void
    {
        $instant = CarbonImmutable::parse('2026-09-22 10:15');

        $result = $this->hours->nextSlotAtOrAfter($instant);

        $this->assertTrue($instant->equalTo($result));
    }

    #[Test]
    public function fits_without_crossing_lunch_is_true_within_morning_block(): void
    {
        $this->assertTrue($this->hours->fitsWithoutCrossingLunch(
            CarbonImmutable::parse('2026-09-22 09:00'),
            CarbonImmutable::parse('2026-09-22 11:00'),
        ));
    }

    #[Test]
    public function fits_without_crossing_lunch_is_false_when_crossing_into_break(): void
    {
        $this->assertFalse($this->hours->fitsWithoutCrossingLunch(
            CarbonImmutable::parse('2026-09-22 11:30'),
            CarbonImmutable::parse('2026-09-22 12:30'),
        ));
    }

    #[Test]
    public function fits_without_crossing_lunch_is_true_within_afternoon_block(): void
    {
        $this->assertTrue($this->hours->fitsWithoutCrossingLunch(
            CarbonImmutable::parse('2026-09-22 14:00'),
            CarbonImmutable::parse('2026-09-22 18:00'),
        ));
    }

    #[Test]
    public function fits_without_crossing_lunch_is_false_when_spanning_the_whole_break(): void
    {
        $this->assertFalse($this->hours->fitsWithoutCrossingLunch(
            CarbonImmutable::parse('2026-09-22 11:00'),
            CarbonImmutable::parse('2026-09-22 14:30'),
        ));
    }

    #[Test]
    public function can_fit_on_day_is_true_when_ending_exactly_at_noon(): void
    {
        $this->assertTrue($this->hours->canFitOnDay(CarbonImmutable::parse('2026-09-22 10:00'), 120));
    }

    #[Test]
    public function can_fit_on_day_is_false_when_ending_past_closing(): void
    {
        $this->assertFalse($this->hours->canFitOnDay(CarbonImmutable::parse('2026-09-22 17:00'), 90));
    }

    #[Test]
    public function can_fit_on_day_is_true_when_ending_exactly_at_closing(): void
    {
        $this->assertTrue($this->hours->canFitOnDay(CarbonImmutable::parse('2026-09-22 17:00'), 60));
    }
}
