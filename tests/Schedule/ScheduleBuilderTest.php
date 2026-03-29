<?php

declare(strict_types=1);

namespace GearsDigital\WeAreOpen\Tests\Schedule;

use GearsDigital\WeAreOpen\Model\BusinessHoursDay;
use GearsDigital\WeAreOpen\Schedule\ScheduleBuilder;

final class ScheduleBuilderTest extends \KirbyTestCase
{
    private ScheduleBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = new ScheduleBuilder();
    }

    public function test_builds_five_weekdays_by_default(): void
    {
        $days = $this->builder->build($this->standardOpenHoursModel());
        $this->assertCount(5, $days);
    }

    public function test_returns_business_hours_day_instances(): void
    {
        $days = $this->builder->build($this->standardOpenHoursModel());
        foreach ($days as $day) {
            $this->assertInstanceOf(BusinessHoursDay::class, $day);
        }
    }

    public function test_open_days_have_slots(): void
    {
        $days = $this->builder->build($this->standardOpenHoursModel());
        foreach ($days as $day) {
            $this->assertNotEmpty($day->slots, "Expected {$day->weekday} to have slots");
        }
    }

    public function test_empty_model_returns_closed_days(): void
    {
        $days = $this->builder->build([]);
        $this->assertCount(5, $days); // Still returns 5 weekdays
        foreach ($days as $day) {
            $this->assertEmpty($day->slots); // But they are all closed
        }
    }
}
