<?php

declare(strict_types=1);

namespace GearsDigital\WeAreOpen\Model;

use Throwable;

/**
 * Normalized and validated options for BusinessHoursListService.
 *
 * WHY:
 * - Central place for validation and defaults
 * - Strong typing for the rest of the service
 * - Prevents invalid states from leaking into business logic
 */
final readonly class BusinessHoursListOptions
{
    /**
     * PHP date() format characters that make sense for a *weekday*
     * (as opposed to day-of-month/year characters like 'd' or 'j').
     * 'D'/'l' render a localized name; 'N'/'w' render PHP's own numeric
     * weekday, exactly as documented for date().
     */
    public const array WEEKDAY_FORMATS = ['D', 'l', 'N', 'w'];

    public function __construct(
        public bool $hideClosedDays,
        public bool $hideWeekends,
        public string $weekdayFormat, // guaranteed: one of self::WEEKDAY_FORMATS
        public ?string $timeFormat,
        public string $locale,
        public string $timezone,
        public int $groupMinSize,
        public string $groupDaySeparator,
    ) {
    }

    /**
     * Create options from array with validation and defaults.
     *
     * @param  array<string,mixed>  $options
     * @return self
     */
    public static function fromArray(array $options): self
    {
        // Restricted to PHP date()'s own weekday characters (case-sensitive,
        // same as date() itself — 'D' and 'd' are not interchangeable).
        $weekdayFormat = (string)($options['weekdayFormat'] ?? 'D');
        if (!in_array($weekdayFormat, self::WEEKDAY_FORMATS, true)) {
            $weekdayFormat = 'D';
        }

        // Locale auto-detection (best-effort). Works even when used outside Kirby.
        $locale = $options['locale'] ?? null;
        if (!is_string($locale) || $locale === '') {
            try {
                $lang = function_exists('kirby') ? kirby()->language() : null;
                $locale = $lang?->locale(LC_TIME) ?: $lang?->code();
            } catch (Throwable) {
                $locale = null;
            }
        }
        $locale = is_string($locale) && $locale !== '' ? $locale : 'de_DE';

        // Timezone fallback.
        $timezone = $options['timezone'] ?? null;
        $timezone = is_string($timezone) && $timezone !== '' ? $timezone : date_default_timezone_get();

        // Optional time formatting for template-friendly output.
        $timeFormat = $options['timeFormat'] ?? 'G:i';
        $timeFormat = is_string($timeFormat) && $timeFormat !== '' ? $timeFormat : null;

        // Grouping options live in the same options DTO to avoid another config object.
        $groupMinSize = max(2, (int)($options['groupMinSize'] ?? 2));
        $groupDaySeparator = is_string($options['groupDaySeparator'] ?? null) && $options['groupDaySeparator'] !== '' ? (string)$options['groupDaySeparator'] : '-';

        return new self(
            hideClosedDays: (bool)($options['hideClosedDays'] ?? false),
            hideWeekends: (bool)($options['hideWeekends'] ?? true),
            weekdayFormat: $weekdayFormat,
            timeFormat: $timeFormat,
            locale: $locale,
            timezone: $timezone,
            groupMinSize: $groupMinSize,
            groupDaySeparator: $groupDaySeparator,
        );
    }
}
