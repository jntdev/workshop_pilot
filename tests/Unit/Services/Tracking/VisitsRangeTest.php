<?php

namespace Tests\Unit\Services\Tracking;

use App\Enums\VisitsPeriod;
use App\Services\Tracking\VisitsRange;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class VisitsRangeTest extends TestCase
{
    private CarbonImmutable $saturday;

    protected function setUp(): void
    {
        parent::setUp();
        $this->saturday = CarbonImmutable::parse('2026-10-10 15:30:00');
    }

    #[Test]
    public function a_daily_period_ends_today_with_one_point_per_day(): void
    {
        $range = VisitsRange::forPeriod(VisitsPeriod::Last30Days, $this->saturday, null);

        $this->assertSame('2026-09-11', $range->start->toDateString());
        $this->assertSame('2026-10-10', $range->end->toDateString());
        $this->assertSame('day', $range->granularity());
        $this->assertSame(30, $range->dayCount());

        $buckets = $range->bucketStarts();
        $this->assertCount(30, $buckets);
        $this->assertSame('2026-09-11', $buckets[0]);
        $this->assertSame('2026-10-10', $buckets[29]);
    }

    #[Test]
    public function a_weekly_period_starts_on_a_monday_with_one_point_per_week(): void
    {
        $range = VisitsRange::forPeriod(VisitsPeriod::Last12Months, $this->saturday, null);

        $this->assertSame('2025-10-13', $range->start->toDateString());
        $this->assertSame('2026-10-10', $range->end->toDateString());
        $this->assertSame('week', $range->granularity());

        $buckets = $range->bucketStarts();
        $this->assertCount(52, $buckets);
        $this->assertSame('2026-10-05', $buckets[51]);
    }

    #[Test]
    public function the_period_starts_at_the_first_tracked_day_when_tracking_began_during_it(): void
    {
        $range = VisitsRange::forPeriod(VisitsPeriod::Last3Months, $this->saturday, CarbonImmutable::parse('2026-09-01'));

        $this->assertSame('2026-09-01', $range->start->toDateString());
        $this->assertCount(40, $range->bucketStarts());
    }

    #[Test]
    public function a_weekly_period_starts_on_the_monday_of_the_first_tracked_week(): void
    {
        $range = VisitsRange::forPeriod(VisitsPeriod::Last12Months, $this->saturday, CarbonImmutable::parse('2026-04-14'));

        $this->assertSame('2026-04-13', $range->start->toDateString());
        $this->assertCount(26, $range->bucketStarts());
    }

    #[Test]
    public function a_first_tracked_day_before_the_period_leaves_it_unchanged(): void
    {
        $range = VisitsRange::forPeriod(VisitsPeriod::Last7Days, $this->saturday, CarbonImmutable::parse('2026-01-01'));

        $this->assertSame('2026-10-04', $range->start->toDateString());
    }

    #[Test]
    public function a_first_tracked_day_after_today_leaves_the_period_unchanged(): void
    {
        $range = VisitsRange::forPeriod(VisitsPeriod::Last7Days, $this->saturday, CarbonImmutable::parse('2026-10-11'));

        $this->assertSame('2026-10-04', $range->start->toDateString());
        $this->assertCount(7, $range->bucketStarts());
    }

    #[Test]
    public function the_previous_period_has_the_same_length_and_ends_the_day_before(): void
    {
        $previous = VisitsRange::forPeriod(VisitsPeriod::Last30Days, $this->saturday, null)->previous();

        $this->assertSame('2026-08-12', $previous->start->toDateString());
        $this->assertSame('2026-09-10', $previous->end->toDateString());
        $this->assertSame(30, $previous->dayCount());
    }

    #[Test]
    public function days_are_grouped_into_weeks_starting_on_monday(): void
    {
        $range = VisitsRange::forPeriod(VisitsPeriod::Last6Months, $this->saturday, null);

        $this->assertSame('2026-09-28', $range->bucketOf('2026-10-04'));
        $this->assertSame('2026-10-05', $range->bucketOf('2026-10-05'));
        $this->assertSame('2026-10-05', $range->bucketOf('2026-10-10'));
    }

    #[Test]
    public function each_day_is_its_own_point_in_a_daily_period(): void
    {
        $range = VisitsRange::forPeriod(VisitsPeriod::Last7Days, $this->saturday, null);

        $this->assertSame('2026-10-04', $range->bucketOf('2026-10-04'));
    }
}
