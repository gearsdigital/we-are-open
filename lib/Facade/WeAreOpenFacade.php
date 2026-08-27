<?php

declare(strict_types=1);

namespace GearsDigital\WeAreOpen\Facade;

use GearsDigital\WeAreOpen\Model\BusinessHoursDay;
use GearsDigital\WeAreOpen\Support\BusinessHoursListService;
use GearsDigital\WeAreOpen\Support\SiteYamlKeys;
use GearsDigital\WeAreOpen\Support\SiteYamlReader;

/**
 * WeAreOpenFacade
 *
 * Public facade for accessing opening hours via Kirby site methods.
 *
 * This facade intentionally exists instead of exposing raw site fields
 * (e.g. `site()->schedule()` or `site()->openhours()`).
 *
 * Reasons for using a facade:
 * - Decouples templates from site content structure (YAML, fields, keys)
 * - Provides a stable, explicit API (contract) for consumers
 * - Encapsulates business logic, validation and defaults
 * - Prevents business rules from leaking into templates
 * - Allows internal refactoring without breaking templates
 * - Enables clean Free / Pro feature separation behind the same API
 *
 * Templates and snippets should never access opening hour fields directly.
 * Instead, they should always rely on this facade.
 *
 * Example usage (Kirby site method):
 *
 * ```php
 * site()->weAreOpen()->businessHours();
 * ```
 *
 * This ensures that templates work with domain models instead of raw data
 * and remain stable even if the underlying implementation changes.
 */
final readonly class WeAreOpenFacade
{
    /**
     * Returns the configured business hours as a flat list of domain objects.
     *
     * The opening hours are read from the `openhours` section of site.yml
     * and converted into {@see BusinessHoursDay} domain models.
     *
     * This method represents the public contract for accessing business hours.
     * Internally, the data source, structure or applied logic may change,
     * but the returned domain models remain stable.
     *
     * @return BusinessHoursDay[]
     *     List of business hours per weekday
     */
    public function businessHours(): array
    {
        $openhours = SiteYamlReader::get(SiteYamlKeys::OPENHOURS);

        // Match the documented contract (README: "one per weekday, Mon–Sun",
        // `$day->label` e.g. "Monday") rather than BusinessHoursListService's
        // own defaults, which are tuned for the (scheduleTable:) tag instead.
        return BusinessHoursListService::build($openhours, [
            'hideWeekends' => false,
            'weekdayFormat' => 'l',
        ]);
    }
}

