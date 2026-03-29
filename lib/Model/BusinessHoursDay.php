<?php

declare(strict_types=1);

namespace GearsDigital\WeAreOpen\Model;

/**
 * Represents one weekday entry (non-grouped).
 *
 * Exposes a strongly typed Weekday instead of a free-form string.
 * Improves template DX (autocomplete, fewer implicit assumptions).
 */
final readonly class BusinessHoursDay
{
    /**
     * @param  TimeRange[]  $slots
     * @param  TimeRange[]  $formattedSlots
     */
    public function __construct(
        public Weekday $weekday,
        public string $label,
        public array $slots,
        public array $formattedSlots,
    ) {
    }
}
