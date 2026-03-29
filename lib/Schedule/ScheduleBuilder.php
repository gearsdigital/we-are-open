<?php

declare(strict_types=1);

namespace GearsDigital\WeAreOpen\Schedule;

use GearsDigital\WeAreOpen\Contracts\ScheduleBuilderInterface;
use GearsDigital\WeAreOpen\Support\BusinessHoursListService;

/**
 * Instance-based schedule builder (FREE version).
 *
 * Delegates to BusinessHoursListService and returns BusinessHoursDay objects directly.
 * No wrapper, no metadata - FREE version only handles regular opening hours in flat list.
 *
 * WHY:
 * - Makes service testable via dependency injection
 * - Allows PRO to decorate with custom logic
 * - Simple pass-through to existing BusinessHoursListService
 */
final class ScheduleBuilder implements ScheduleBuilderInterface
{
    /**
     * Build flat list.
     *
     * @param  array<int, array{weekday:string, slots:array<int,array{start:string,end:string}>}>  $model
     * @param  array<string,mixed>  $options
     * @return array
     */
    public function build(array $model, array $options = []): array
    {
        return BusinessHoursListService::build($model, $options);
    }
}
