<?php

declare(strict_types=1);

namespace GearsDigital\WeAreOpen\Contracts;

/**
 * ScheduleTableBuilder
 *
 * WHY:
 * - Decouple KirbyTag parsing / presentation needs from domain list-building.
 * - Allow PRO to extend rendering capabilities (grouping, raw model exposure)
 *   without duplicating tag callbacks and snippet code.
 *
 * The builder is responsible for producing a "prepared" DTO list used by snippets/templates.
 */
interface ScheduleTableBuilder
{
    /**
     * Build the prepared DTO list for rendering.
     *
     * @param array<int, mixed> $rawHours Plain model as read from Kirby (YAML -> array)
     * @param array<string, mixed> $options Normalized options (layout, formatting, filters, etc.)
     * @return array<int, mixed> Prepared DTO list (e.g. BusinessHoursDay[] or BusinessHoursGroup[])
     */
    public function buildPrepared(array $rawHours, array $options): array;

    /**
     * Whether this builder supports exposing raw data to the snippet/template.
     */
    public function supportsRawData(): bool;

    /**
     * Whether this builder supports the given layout.
     * Example layouts: flat, consecutive, byhours
     */
    public function supportsLayout(string $layout): bool;
}
