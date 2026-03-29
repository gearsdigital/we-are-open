<?php

declare(strict_types=1);

namespace GearsDigital\WeAreOpen\Contracts;


/**
 * Schedule builder contract (FREE version).
 *
 * Transforms raw opening hours model into presentation-ready objects.
 * FREE version returns BusinessHoursDay objects in a flat list.
 *
 * WHY:
 * - Makes service testable via dependency injection
 * - Keeps business logic separate from Kirby integration
 * - Simple contract for basic opening hours display
 */
interface ScheduleBuilderInterface
{
    /**
     * Build flat list (one entry per weekday).
     *
     * @param  array<int, array{weekday:string, slots:array<int,array{start:string,end:string}>}>  $model
     * @param  array<string,mixed>  $options
     * @return array
     */
    public function build(array $model, array $options = []): array;
}
