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

        // Locale resolution (best-effort, also works outside Kirby):
        //   1. explicit 'locale' option passed to the service
        //   2. the active Kirby language (multi-language sites)
        //   3. Kirby's site-wide `locale` config option (single-language sites
        //      have no kirby()->language(), so this is their way in)
        //   4. 'en' — a plain, predictable default; a hardcoded 'de_DE' used to
        //      silently render every weekday name in German here.
        $locale = $options['locale'] ?? null;
        if (!is_string($locale) || $locale === '') {
            try {
                $lang = function_exists('kirby') ? kirby()->language() : null;
                $locale = $lang?->locale(LC_TIME) ?: $lang?->code() ?: self::kirbyConfigLocale();
            } catch (Throwable) {
                $locale = null;
            }
        }
        $locale = is_string($locale) && $locale !== '' ? $locale : 'en';

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

    /**
     * Kirby's site-wide `locale` config option, normalized to an intl locale id.
     *
     * The option may be a plain string ('de_DE.UTF-8') or an array keyed by
     * LC_* constants; either way the charset suffix is dropped.
     */
    private static function kirbyConfigLocale(): ?string
    {
        if (!function_exists('kirby')) {
            return null;
        }

        $locale = kirby()->option('locale');
        if (is_array($locale)) {
            $locale = $locale[LC_TIME] ?? $locale[LC_ALL] ?? null;
        }
        if (!is_string($locale) || $locale === '') {
            return null;
        }

        return strstr($locale, '.', true) ?: $locale; // "de_DE.UTF-8" -> "de_DE"
    }
}
