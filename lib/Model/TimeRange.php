<?php

declare(strict_types=1);

namespace GearsDigital\WeAreOpen\Model;

/**
 * Represents a single opening time slot.
 *
 * WHY:
 * - Explicit structure instead of array shapes
 * - Clear distinction between raw and formatted values
 */
final readonly class TimeRange
{
    public function __construct(
        public string $start,
        public string $end,
    ) {
    }
}
