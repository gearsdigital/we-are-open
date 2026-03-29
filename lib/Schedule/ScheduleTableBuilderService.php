<?php

declare(strict_types=1);

namespace GearsDigital\WeAreOpen\Schedule;

use GearsDigital\WeAreOpen\Contracts\ScheduleTableBuilder;
use GearsDigital\WeAreOpen\Support\BusinessHoursListService;

/**
 * FreeScheduleTableBuilder
 *
 * Free version intentionally supports ONLY a flat table.
 * Any requested grouping layout is ignored/forced to 'flat' here.
 */
final class ScheduleTableBuilderService implements ScheduleTableBuilder
{
    public function buildPrepared(array $rawHours, array $options): array
    {
        // Force flat output in Free, regardless of requested layout.
        $options['layout'] = 'flat';

        return BusinessHoursListService::build($rawHours, $options);
    }

    public function supportsRawData(): bool
    {
        return false;
    }

    public function supportsLayout(string $layout): bool
    {
        $layout = strtolower(trim($layout));

        return $layout === '' || $layout === 'flat';
    }
}
