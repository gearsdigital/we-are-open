<?php

declare(strict_types=1);

namespace GearsDigital\WeAreOpen\Model;

use DateTimeInterface;

/**
 * Weekday
 *
 * WHY THIS DTO EXISTS
 * -------------------
 * Weekdays are used throughout the opening hours domain:
 * - ordering (Mon → Sun)
 * - weekend detection
 * - grouping of consecutive days
 *
 * Using a plain string ("mon", "tue", …) spreads implicit rules
 * across the codebase. This DTO centralizes those rules and
 * guarantees a valid weekday state.
 *
 * DESIGN PRINCIPLES
 * -----------------
 * - Immutable value object
 * - No localization or formatting logic
 * - No framework dependencies
 * - Strictly limited to valid weekday identifiers
 */
final class Weekday
{
    /**
     * Canonical weekday order (ISO 8601, Monday first).
     * Index 0-6 maps to ISO weekday numbers 1-7.
     */
    public const array ORDER = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];

    /**
     * Weekdays considered weekend.
     */
    private const array WEEKEND = ['sat', 'sun'];

    private function __construct(
        public readonly string $value
    ) {
    }

    /**
     * Create a Weekday from string.
     *
     * Returns null for invalid values to allow safe, defensive usage
     * when consuming user or CMS-provided data.
     */
    public static function fromString(string $value): ?self
    {
        $value = strtolower(trim($value));

        if (!in_array($value, self::ORDER, true)) {
            return null;
        }

        return new self($value);
    }

    /**
     * Create a Weekday from ISO 8601 weekday number (1=Monday, 7=Sunday).
     *
     * Returns null for invalid numbers.
     */
    public static function fromIsoNumber(int $isoNumber): ?self
    {
        $index = $isoNumber - 1; // ISO is 1-based, ORDER is 0-based
        $value = self::ORDER[$index] ?? null;

        return $value !== null ? new self($value) : null;
    }

    /**
     * Create a Weekday from a date.
     *
     * Extracts the ISO weekday number from the date.
     */
    public static function fromDate(DateTimeInterface $date): ?self
    {
        return self::fromIsoNumber((int) $date->format('N'));
    }

    /**
     * Return zero-based index according to canonical weekday order.
     *
     * Example:
     * - mon => 0
     * - tue => 1
     * - ...
     */
    public function index(): int
    {
        /** @var int */
        return array_search($this->value, self::ORDER, true);
    }

    /**
     * Whether this weekday is a weekend day.
     */
    public function isWeekend(): bool
    {
        return in_array($this->value, self::WEEKEND, true);
    }

    /**
     * Whether the given weekday directly follows this one.
     *
     * Used for grouping consecutive days (e.g. Mon–Fri).
     */
    public function isConsecutiveTo(self $next): bool
    {
        return $next->index() === $this->index() + 1;
    }

    /**
     * String representation ("mon", "tue", …).
     *
     * Useful for array keys, comparisons, and template output.
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
