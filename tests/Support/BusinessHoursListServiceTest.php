<?php

declare(strict_types=1);

namespace GearsDigital\WeAreOpen\Tests\Support;

use GearsDigital\WeAreOpen\Model\BusinessHoursDay;
use GearsDigital\WeAreOpen\Support\BusinessHoursListService;

final class BusinessHoursListServiceTest extends \KirbyTestCase
{
    private function build(array $options = []): array
    {
        return BusinessHoursListService::build($this->standardOpenHoursModel(), $options);
    }

    public function test_returns_five_weekdays_by_default(): void
    {
        // hideWeekends defaults to true — weekends are excluded
        $days = $this->build();
        $this->assertCount(5, $days);
    }

    public function test_returns_seven_days_when_weekends_included(): void
    {
        $model = $this->standardOpenHoursModel();
        $model[] = ['weekday' => 'sat', 'slots' => []];
        $model[] = ['weekday' => 'sun', 'slots' => []];

        $days = BusinessHoursListService::build($model, ['hideWeekends' => false]);
        $this->assertCount(7, $days);
    }

    public function test_days_are_business_hours_day_instances(): void
    {
        $days = $this->build();
        foreach ($days as $day) {
            $this->assertInstanceOf(BusinessHoursDay::class, $day);
        }
    }

    public function test_monday_has_one_slot(): void
    {
        $days = $this->build();
        $monday = $days[0];
        $this->assertCount(1, $monday->slots);
        $this->assertSame('08:00:00', $monday->slots[0]->start);
        $this->assertSame('17:00:00', $monday->slots[0]->end);
    }

    public function test_days_are_in_monday_to_friday_order(): void
    {
        $days = $this->build();
        $weekdays = array_map(fn ($d) => (string) $d->weekday, $days);
        $this->assertSame(['mon', 'tue', 'wed', 'thu', 'fri'], $weekdays);
    }

    public function test_time_format_H_i_adds_leading_zero(): void
    {
        // 08:00:00 with H:i format → "08:00"
        $days = $this->build(['timeFormat' => 'H:i']);
        $monday = $days[0];
        $this->assertSame('08:00', $monday->formattedSlots[0]->start);
        $this->assertSame('17:00', $monday->formattedSlots[0]->end);
    }

    public function test_time_format_G_i_removes_leading_zero(): void
    {
        // 08:00:00 with G:i format → "8:00"
        $days = $this->build(['timeFormat' => 'G:i']);
        $monday = $days[0];
        $this->assertSame('8:00', $monday->formattedSlots[0]->start);
    }

    public function test_raw_slots_are_never_modified_by_time_format(): void
    {
        $days = $this->build(['timeFormat' => 'H:i']);
        $monday = $days[0];
        $this->assertSame('08:00:00', $monday->slots[0]->start);
    }

    public function test_closed_day_has_no_slots(): void
    {
        $model = $this->standardOpenHoursModel();
        foreach ($model as &$row) {
            if ($row['weekday'] === 'wed') {
                $row['slots'] = [];
            }
        }
        unset($row);

        $days = BusinessHoursListService::build($model, []);
        $wednesday = null;
        foreach ($days as $day) {
            if ((string) $day->weekday === 'wed') {
                $wednesday = $day;
                break;
            }
        }

        $this->assertNotNull($wednesday);
        $this->assertEmpty($wednesday->slots);
    }

    public function test_closed_days_are_excluded_when_hide_closed_days_is_true(): void
    {
        $model = $this->standardOpenHoursModel();
        foreach ($model as &$row) {
            if ($row['weekday'] === 'wed') {
                $row['slots'] = [];
            }
        }
        unset($row);

        $days = BusinessHoursListService::build($model, ['hideClosedDays' => true]);
        $weekdays = array_map(fn ($d) => (string) $d->weekday, $days);
        $this->assertNotContains('wed', $weekdays);
        $this->assertCount(4, $days);
    }

    public function test_day_marked_closed_has_no_slots_even_if_slots_are_stored(): void
    {
        // The panel keeps a day's slots after it's toggled closed (so
        // re-opening the day restores the previous hours). The rendered
        // output must not treat that day as open.
        $model = $this->standardOpenHoursModel();
        foreach ($model as &$row) {
            if ($row['weekday'] === 'mon') {
                $row['isOpen'] = false;
            }
        }
        unset($row);

        $days = BusinessHoursListService::build($model, []);
        $monday = $days[0];

        $this->assertSame('mon', (string) $monday->weekday);
        $this->assertEmpty($monday->slots);
        $this->assertEmpty($monday->formattedSlots);
    }

    public function test_day_marked_closed_is_excluded_when_hide_closed_days_is_true(): void
    {
        $model = $this->standardOpenHoursModel();
        foreach ($model as &$row) {
            if ($row['weekday'] === 'mon') {
                $row['isOpen'] = false;
            }
        }
        unset($row);

        $days = BusinessHoursListService::build($model, ['hideClosedDays' => true]);
        $weekdays = array_map(fn ($d) => (string) $d->weekday, $days);
        $this->assertNotContains('mon', $weekdays);
    }

    public function test_label_is_non_empty_string(): void
    {
        $days = $this->build();
        foreach ($days as $day) {
            $this->assertNotEmpty($day->label);
        }
    }
}
