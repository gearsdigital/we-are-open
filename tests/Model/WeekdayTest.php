<?php

declare(strict_types=1);

namespace GearsDigital\WeAreOpen\Tests\Model;

use GearsDigital\WeAreOpen\Model\Weekday;
use PHPUnit\Framework\TestCase;

final class WeekdayTest extends TestCase
{
    public function test_from_string_returns_weekday_for_valid_code(): void
    {
        $wd = Weekday::fromString('mon');
        $this->assertNotNull($wd);
        $this->assertSame('mon', (string) $wd);
    }

    public function test_from_string_returns_null_for_invalid_code(): void
    {
        $this->assertNull(Weekday::fromString('xyz'));
    }

    public function test_from_string_is_case_insensitive(): void
    {
        $wd = Weekday::fromString('MON');
        $this->assertNotNull($wd);
        $this->assertSame('mon', (string) $wd);
    }

    public function test_is_weekend_returns_true_for_saturday(): void
    {
        $sat = Weekday::fromString('sat');
        $this->assertNotNull($sat);
        $this->assertTrue($sat->isWeekend());
    }

    public function test_is_weekend_returns_true_for_sunday(): void
    {
        $sun = Weekday::fromString('sun');
        $this->assertNotNull($sun);
        $this->assertTrue($sun->isWeekend());
    }

    public function test_is_weekend_returns_false_for_monday(): void
    {
        $mon = Weekday::fromString('mon');
        $this->assertNotNull($mon);
        $this->assertFalse($mon->isWeekend());
    }

    public function test_is_weekend_returns_false_for_friday(): void
    {
        $fri = Weekday::fromString('fri');
        $this->assertNotNull($fri);
        $this->assertFalse($fri->isWeekend());
    }

    public function test_index_returns_zero_for_monday(): void
    {
        $mon = Weekday::fromString('mon');
        $this->assertNotNull($mon);
        $this->assertSame(0, $mon->index());
    }

    public function test_index_returns_six_for_sunday(): void
    {
        $sun = Weekday::fromString('sun');
        $this->assertNotNull($sun);
        $this->assertSame(6, $sun->index());
    }

    public function test_is_consecutive_to_returns_true_for_next_day(): void
    {
        $mon = Weekday::fromString('mon');
        $tue = Weekday::fromString('tue');
        $this->assertNotNull($mon);
        $this->assertNotNull($tue);
        $this->assertTrue($mon->isConsecutiveTo($tue));
    }

    public function test_is_consecutive_to_returns_false_for_same_day(): void
    {
        $mon = Weekday::fromString('mon');
        $this->assertNotNull($mon);
        $this->assertFalse($mon->isConsecutiveTo($mon));
    }

    public function test_is_consecutive_to_returns_false_for_non_adjacent_day(): void
    {
        $mon = Weekday::fromString('mon');
        $wed = Weekday::fromString('wed');
        $this->assertNotNull($mon);
        $this->assertNotNull($wed);
        $this->assertFalse($mon->isConsecutiveTo($wed));
    }

    /**
     * @dataProvider validWeekdayCodesProvider
     */
    public function test_all_valid_codes_are_accepted(string $code): void
    {
        $wd = Weekday::fromString($code);
        $this->assertNotNull($wd, "Expected '$code' to be a valid weekday code");
    }

    public static function validWeekdayCodesProvider(): array
    {
        return [
            ['mon'], ['tue'], ['wed'], ['thu'], ['fri'], ['sat'], ['sun'],
        ];
    }
}
