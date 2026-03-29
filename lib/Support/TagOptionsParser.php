<?php

declare(strict_types=1);

namespace GearsDigital\WeAreOpen\Support;

use Kirby\Text\KirbyTag;

final class TagOptionsParser
{
    /**
     * @return array{groupDays:?bool, showClosed:bool, timeFormat:?string}
     */
    public static function parse(KirbyTag $tag): array
    {
        $groupDays = null;
        if ($tag->layout !== null) {
            $layoutValue = strtolower(trim((string)$tag->layout));
            $groupDays = $layoutValue === 'grouped';
        }

        $showClosed = option('gearsdigital.we-are-open.showClosedDays', true);
        if ($tag->showClosed !== null) {
            $showClosedValue = strtolower(trim((string)$tag->showClosed));
            $showClosed = in_array($showClosedValue, ['true', 'yes', '1'], true);
        }

        $timeFormat = null;
        if (property_exists($tag, 'timeFormat') && $tag->timeFormat !== null) {
            $tf = trim((string)$tag->timeFormat);
            if ($tf !== '') {
                $timeFormat = $tf;
            }
        }

        return ['groupDays' => $groupDays, 'showClosed' => $showClosed, 'timeFormat' => $timeFormat];
    }
}
