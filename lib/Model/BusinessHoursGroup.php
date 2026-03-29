<?php

declare(strict_types=1);

namespace GearsDigital\WeAreOpen\Model;

/**
 * Represents a grouped range of weekdays (e.g. Mon–Fri).
 *
 * WHY:
 * - Uses Weekday[] to guarantee valid weekday identifiers.
 * - Keeps grouping logic explicit while providing strong typing to templates.
 */
final readonly class BusinessHoursGroup
{
    /**
     * @param  Weekday[]  $weekdays
     * @param  TimeRange[]  $slots
     * @param  TimeRange[]  $formattedSlots
     */
    public function __construct(
        public array $weekdays,
        public string $label,
        public array $slots,
        public array $formattedSlots,
        public bool $isClosed,
        public bool $isWeekend,
    ) {
    }
}
