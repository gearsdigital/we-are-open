<?php

declare(strict_types=1);

namespace GearsDigital\WeAreOpen\Tests\Model;

use GearsDigital\WeAreOpen\Model\BusinessHoursDay;
use GearsDigital\WeAreOpen\Model\TimeRange;
use GearsDigital\WeAreOpen\Model\Weekday;
use PHPUnit\Framework\TestCase;

final class BusinessHoursDayTest extends TestCase
{
    private function makeMonday(): BusinessHoursDay
    {
        $weekday = Weekday::fromString('mon');
        assert($weekday !== null);
        $slot = new TimeRange('08:00:00', '17:00:00');

        return new BusinessHoursDay(
            weekday: $weekday,
            label: 'Monday',
            slots: [$slot],
            formattedSlots: [$slot],
        );
    }

    public function test_label_is_accessible(): void
    {
        $day = $this->makeMonday();
        $this->assertSame('Monday', $day->label);
    }

    public function test_weekday_is_accessible(): void
    {
        $day = $this->makeMonday();
        $this->assertSame('mon', (string) $day->weekday);
    }

    public function test_slots_returns_time_range_array(): void
    {
        $day = $this->makeMonday();
        $this->assertCount(1, $day->slots);
        $this->assertInstanceOf(TimeRange::class, $day->slots[0]);
    }

    public function test_formatted_slots_returns_time_range_array(): void
    {
        $day = $this->makeMonday();
        $this->assertCount(1, $day->formattedSlots);
        $this->assertInstanceOf(TimeRange::class, $day->formattedSlots[0]);
    }

    public function test_empty_slots_represents_closed_day(): void
    {
        $weekday = Weekday::fromString('sat');
        assert($weekday !== null);
        $day = new BusinessHoursDay(
            weekday: $weekday,
            label: 'Saturday',
            slots: [],
            formattedSlots: [],
        );

        $this->assertEmpty($day->slots);
        $this->assertTrue(empty($day->slots));
    }
}
