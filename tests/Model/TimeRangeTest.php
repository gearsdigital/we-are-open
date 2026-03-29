<?php

declare(strict_types=1);

namespace GearsDigital\WeAreOpen\Tests\Model;

use GearsDigital\WeAreOpen\Model\TimeRange;
use PHPUnit\Framework\TestCase;

final class TimeRangeTest extends TestCase
{
    public function test_start_and_end_are_accessible(): void
    {
        $slot = new TimeRange('08:00:00', '17:00:00');
        $this->assertSame('08:00:00', $slot->start);
        $this->assertSame('17:00:00', $slot->end);
    }

    public function test_properties_are_strings(): void
    {
        $slot = new TimeRange('09:00:00', '12:30:00');
        $this->assertIsString($slot->start);
        $this->assertIsString($slot->end);
    }
}
