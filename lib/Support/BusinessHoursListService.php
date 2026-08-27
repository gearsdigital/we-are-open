<?php

declare(strict_types=1);

namespace GearsDigital\WeAreOpen\Support;

use DateInvalidTimeZoneException;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use GearsDigital\WeAreOpen\Model\BusinessHoursDay;
use GearsDigital\WeAreOpen\Model\BusinessHoursListOptions;
use GearsDigital\WeAreOpen\Model\TimeRange;
use GearsDigital\WeAreOpen\Model\Weekday;
use IntlDateFormatter;
use Throwable;

/**
 * BusinessHoursListService
 *
 * WHY THIS SERVICE EXISTS
 * -----------------------
 * Kirby stores opening hours in a UI-friendly model (weekday + time slots).
 * Templates typically need a presentation-oriented structure:
 * - stable weekday order (Mon → Sun)
 * - optional filtering (hide closed days, hide weekends)
 * - localized weekday labels
 * - optional time formatting (e.g. "G:i")
 *
 * This service performs that transformation and returns DTOs (not arrays) for better DX.
 *
 * KEY SEMANTICS
 * -------------
 * - isClosed is TRUE if there are no time slots OR if isOpen is explicitly set to false.
 *
 * LOCALIZATION STRATEGY
 * ---------------------
 * 1) Prefer ext-intl (IntlDateFormatter) if available.
 * 2) Otherwise, use an optional plugin config map:
 *    option('gearsdigital.we-are-open.weekdayMap')
 * 3) Last resort fallback: ucfirst('mon') => 'Mon'
 *
 * IMPORTANT: weekdayFormat is intentionally restricted to keep logic simple:
 * - 'D' => short weekday label
 * - 'l' => long weekday label
 *
 * NOTE: Grouping functionality (consecutive and by-hours) is a PRO-only feature
 * and is implemented via GroupingStrategy pattern in the PRO plugin.
 */
final class BusinessHoursListService
{
    /**
     * Canonical weekday order.
     *
     * NOTE:
     * We keep this local to the service to avoid coupling Weekday to ordering concerns.
     * Weekday still enforces validity and provides weekend/consecutive logic.
     */
    private const array ORDER = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];

    /**
     * A stable Monday reference date used for IntlDateFormatter.
     * 2024-01-01 is a Monday, so mon..sun maps to +0..+6 days.
     */
    private const string BASE_MONDAY_DATE = '2024-01-01';

    /**
     * Build a flat list (Mon → Sun).
     *
     * @param  array<int, array{weekday:string, slots:array<int,array{start:string,end:string}>}>  $model
     * @param  array<string,mixed>  $options
     *
     * @return BusinessHoursDay[]
     */
    public static function build(array $model, array $options): array
    {
        $o = self::options($options);

        return self::buildWithOptions($model, $o);
    }

    /**
     * Internal build that works on a validated Options DTO.
     *
     * WHY:
     * - keeps public API convenient (array options)
     * - keeps internal logic strongly typed and free from defensive checks
     *
     * @param  array<int, array{weekday:string, slots:array<int,array{start:string,end:string}>}>  $model
     *
     * @return BusinessHoursDay[]
     */
    private static function buildWithOptions(array $model, BusinessHoursListOptions $o): array
    {
        $index = self::indexByWeekday($model);

        $rows = [];

        foreach (self::weekdayOrder() as $weekday) {
            if ($o->hideWeekends && $weekday->isWeekend()) {
                continue;
            }

            $slotsArray = self::normalizeSlots($index[(string)$weekday]['slots'] ?? []);
            $isOpen = $index[(string)$weekday]['isOpen'] ?? true;

            // Core semantic rule: closed if no slots exist OR if explicitly marked as closed (isOpen=false).
            $isClosed = empty($slotsArray) || $isOpen === false;

            if ($o->hideClosedDays && $isClosed) {
                continue;
            }

            // A day marked closed (isOpen=false) may still have stored slots
            // (the panel keeps them around so re-opening the day restores the
            // previous hours) — never surface those slots while closed.
            if ($isClosed) {
                $slotsArray = [];
            }

            $formattedSlotsArray = self::formatSlots($slotsArray, $o);

            $rows[] = new BusinessHoursDay(
                weekday: $weekday,
                label: self::weekdayLabel($weekday, $o),
                slots: self::slotsToDTOs($slotsArray),
                formattedSlots: self::slotsToDTOs($formattedSlotsArray),
            );
        }

        return $rows;
    }


    /**
     * Normalize and validate options into a DTO.
     *
     * WHY:
     * - Avoid invalid values leaking into the main logic
     * - Provide strong typing for the rest of the service
     */
    private static function options(array $options): BusinessHoursListOptions
    {
        return BusinessHoursListOptions::fromArray($options);
    }

    /**
     * Return the canonical weekday order as Weekday instances.
     *
     * WHY:
     * - Avoid spreading "magic strings" across the service
     * - Centralize weekday validity and weekday logic in Weekday
     *
     * @return Weekday[]
     */
    private static function weekdayOrder(): array
    {
        $days = [];
        foreach (self::ORDER as $value) {
            $wd = Weekday::fromString($value);
            if ($wd !== null) {
                $days[] = $wd;
            }
        }

        return $days;
    }

    /**
     * Convert normalized slot arrays into DTOs.
     *
     * @param  array<int, array{start:string,end:string}>  $slots
     *
     * @return TimeRange[]
     */
    private static function slotsToDTOs(array $slots): array
    {
        $out = [];
        foreach ($slots as $slot) {
            $out[] = new TimeRange($slot['start'], $slot['end']);
        }

        return $out;
    }


    /**
     * Index input model by weekday (string key).
     *
     * WHY:
     * The CMS model is a list; for canonical iteration we want O(1) access by weekday.
     *
     * @param  array<int, array{weekday:string, slots:array, isOpen?:bool}>  $model
     *
     * @return array<string, array{slots:array, isOpen:bool}>
     */
    private static function indexByWeekday(array $model): array
    {
        $out = [];

        foreach ($model as $row) {
            $wd = Weekday::fromString((string)($row['weekday'] ?? ''));
            if ($wd === null) {
                continue;
            }

            $out[(string)$wd] = [
                'slots' => $row['slots'] ?? [],
                'isOpen' => $row['isOpen'] ?? true,
            ];
        }

        return $out;
    }

    /**
     * Normalize raw slots into the strict shape:
     * [ ['start' => 'HH:MM:SS', 'end' => 'HH:MM:SS'], ... ]
     *
     * WHY:
     * UI/YAML input might contain unexpected values. Normalizing keeps downstream code safe.
     *
     * @param  array<int, mixed>  $slots
     *
     * @return array<int, array{start:string,end:string}>
     */
    private static function normalizeSlots(array $slots): array
    {
        $out = [];

        foreach ($slots as $slot) {
            if (!is_array($slot) || !isset($slot['start'], $slot['end'])) {
                continue;
            }

            // Keep "HH:MM:SS" stable even if input contains longer strings.
            $start = substr(trim((string)$slot['start']), 0, 8);
            $end = substr(trim((string)$slot['end']), 0, 8);

            if ($start === '' || $end === '') {
                continue;
            }

            $out[] = ['start' => $start, 'end' => $end];
        }

        return $out;
    }

    /**
     * Format slot times if timeFormat is provided.
     * Raw slots are never modified; formatting is additive.
     *
     * @param  array<int, array{start:string,end:string}>  $slots
     *
     * @return array<int, array{start:string,end:string}>
     */
    private static function formatSlots(array $slots, BusinessHoursListOptions $o): array
    {
        if ($o->timeFormat === null) {
            return $slots;
        }

        $out = [];
        foreach ($slots as $slot) {
            $out[] = [
                'start' => self::formatTime($slot['start'], $o->timeFormat),
                'end' => self::formatTime($slot['end'], $o->timeFormat),
            ];
        }

        return $out;
    }

    /**
     * @param  string  $time
     * @param  string  $format
     *
     * @return string
     */
    private static function formatTime(string $time, string $format): string
    {
        // A dummy date is sufficient because only the time part is formatted.
        $ts = strtotime(self::BASE_MONDAY_DATE.' '.$time);

        return $ts !== false ? date($format, $ts) : $time;
    }

    /**
     * @param  Weekday  $weekday
     * @param  BusinessHoursListOptions  $o
     *
     * @return string
     */
    private static function weekdayLabel(Weekday $weekday, BusinessHoursListOptions $o): string
    {
        $type = ($o->weekdayFormat === 'l') ? 'long' : 'short';

        return self::weekdayName($weekday, $type, $o);
    }

    /**
     * Localized weekday name, using the smallest possible dependency set.
     *
     * @param  Weekday  $weekday
     * @param  'short'|'long'  $type
     * @param  BusinessHoursListOptions  $o
     *
     * @return string
     */
    private static function weekdayName(Weekday $weekday, string $type, BusinessHoursListOptions $o): string
    {
        // 1) Prefer ext-intl if present (highest quality localization).
        $intl = self::intlWeekdayName($weekday, $type, $o);
        if ($intl !== null) {
            return $intl;
        }

        // 2) Optional plugin-level fallback map for systems without ext-intl.
        $mapped = self::weekdayMapFromOptions($weekday, $type, $o);
        if ($mapped !== null) {
            return $mapped;
        }

        // 3) Last resort developer fallback.
        return ucfirst((string)$weekday);
    }

    /**
     * @param  Weekday  $weekday
     * @param  string  $type
     * @param  BusinessHoursListOptions  $o
     *
     * @return string|null
     */
    private static function intlWeekdayName(Weekday $weekday, string $type, BusinessHoursListOptions $o): ?string
    {
        if (!class_exists(IntlDateFormatter::class)) {
            return null;
        }

        // ICU patterns: EEE = short, EEEE = long.
        $pattern = ($type === 'long') ? 'EEEE' : 'EEE';

        try {
            $dt = self::weekdayToDate($weekday, $o->timezone);

            $idf = new IntlDateFormatter(
                $o->locale,
                IntlDateFormatter::NONE,
                IntlDateFormatter::NONE,
                $o->timezone,
                IntlDateFormatter::GREGORIAN,
                $pattern
            );

            $out = $idf->format($dt);

            return is_string($out) && $out !== '' ? $out : null;
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Optional plugin config fallback:
     * option('gearsdigital.we-are-open.weekdayMap')
     *
     * Supports:
     * - locale scoped map: $map['de']['thu']['short']
     * - global map:        $map['thu']['short']
     *
     * @param  Weekday  $weekday
     * @param  'short'|'long'  $type
     * @param  BusinessHoursListOptions  $o
     *
     * @return string|null
     */
    private static function weekdayMapFromOptions(Weekday $weekday, string $type, BusinessHoursListOptions $o): ?string
    {
        $map = option('gearsdigital.we-are-open.weekdayMap');
        if (!is_array($map)) {
            return null;
        }

        $locale = strtolower($o->locale);
        $lang = $locale !== '' ? substr($locale, 0, 2) : '';

        $key = (string)$weekday;

        if ($lang !== '' && isset($map[$lang]) && is_array($map[$lang])) {
            $val = $map[$lang][$key][$type] ?? null;

            return is_string($val) ? $val : null;
        }

        $val = $map[$key][$type] ?? null;

        return is_string($val) ? $val : null;
    }

    /**
     * @throws DateInvalidTimeZoneException
     * @throws Exception
     */
    private static function weekdayToDate(Weekday $weekday, string $timezone): DateTimeImmutable
    {
        $base = new DateTimeImmutable(self::BASE_MONDAY_DATE, new DateTimeZone($timezone));

        return $base->modify('+'.$weekday->index().' day');
    }

}
