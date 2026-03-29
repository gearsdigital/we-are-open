<?php

declare(strict_types=1);

namespace GearsDigital\WeAreOpen\Schedule;

use GearsDigital\WeAreOpen\Contracts\ScheduleBuilderInterface;

/**
 * Factory for creating ScheduleBuilder instances.
 *
 * Creates FREE builder with no provider dependencies.
 * PRO plugin can decorate with additional functionality.
 */
final class ScheduleBuilderFactory
{
    /**
     * Create default FREE builder.
     */
    public static function create(): ScheduleBuilderInterface
    {
        return new ScheduleBuilder();
    }
}
