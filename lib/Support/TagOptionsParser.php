<?php

declare(strict_types=1);

namespace GearsDigital\WeAreOpen\Support;

use GearsDigital\WeAreOpen\Model\BusinessHoursListOptions;
use Kirby\Text\KirbyTag;

final class TagOptionsParser
{
    /**
     * @return array{groupDays:?bool, hideClosedDays:bool, hideWeekends:bool, timeFormat:?string, weekdayFormat:?string}
     */
    public static function parse(KirbyTag $tag): array
    {
        $groupDays = null;
        if ($tag->layout !== null) {
            $layoutValue = strtolower(trim((string)$tag->layout));
            $groupDays = $layoutValue === 'grouped';
        }

        // NOTE: read the lowercase property here — KirbyTag only promotes
        // registered attributes to $tag->{name} when {name} is lowercase
        // (see the 'attr' list in index.php), regardless of how the
        // attribute is capitalized in the tag itself.
        $showClosed = option('gearsdigital.we-are-open.showClosedDays', true);
        if ($tag->showclosed !== null) {
            $showClosedValue = strtolower(trim((string)$tag->showclosed));
            $showClosed = in_array($showClosedValue, ['true', 'yes', '1'], true);
        }

        $showWeekends = option('gearsdigital.we-are-open.showWeekends', true);
        if ($tag->showweekends !== null) {
            $showWeekendsValue = strtolower(trim((string)$tag->showweekends));
            $showWeekends = in_array($showWeekendsValue, ['true', 'yes', '1'], true);
        }

        $timeFormat = null;
        if (property_exists($tag, 'timeformat') && $tag->timeformat !== null) {
            $tf = trim((string)$tag->timeformat);
            if ($tf !== '') {
                $timeFormat = $tf;
            }
        }

        // Same character set as PHP's own date(): 'D'/'l' for a (localized)
        // name, 'N'/'w' for a numeric weekday — case-sensitive, exactly as
        // date() itself is ('D' and 'd' are unrelated format characters).
        // Anything else falls back to the 'D' default rather than guessing.
        $weekdayFormat = null;
        if ($tag->weekdayformat !== null) {
            $wf = trim((string)$tag->weekdayformat);
            if (in_array($wf, BusinessHoursListOptions::WEEKDAY_FORMATS, true)) {
                $weekdayFormat = $wf;
            }
        }

        // BusinessHoursListOptions consumes "hideClosedDays"/"hideWeekends"
        // (inverse of the tag's user-facing "showClosed"/"showWeekends").
        return [
            'groupDays' => $groupDays,
            'hideClosedDays' => !$showClosed,
            'hideWeekends' => !$showWeekends,
            'timeFormat' => $timeFormat,
            'weekdayFormat' => $weekdayFormat,
        ];
    }
}
