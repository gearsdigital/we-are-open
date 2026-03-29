<?php

declare(strict_types=1);

namespace GearsDigital\WeAreOpen\Schedule;

use GearsDigital\WeAreOpen\Support\SiteYamlKeys;
use GearsDigital\WeAreOpen\Support\SiteYamlReader;
use GearsDigital\WeAreOpen\Support\TagOptionsParser;
use Kirby\Text\KirbyTag;

/**
 * ScheduleTableTagService
 *
 * Single responsibility:
 * - Load raw model
 * - Parse tag options
 * - Build prepared DTO list via builder
 * - Render snippet
 *
 * IMPORTANT:
 * - Must not expose rawData to the snippet
 * - Only supports layout=flat
 */
final class ScheduleTableTagService
{
    public static function render(KirbyTag $tag): string
    {
        $key = SiteYamlKeys::OPENHOURS;
        $raw = SiteYamlReader::get($key);
        $options = TagOptionsParser::parse($tag);

        $builder = new ScheduleTableBuilderService();
        $tableData = $builder->buildPrepared($raw, $options);

        return snippet('we-are-open/business-hours-table', [
            'tableData' => $tableData,
            'options' => $options,
            'tag' => $tag,
        ], true);
    }
}
