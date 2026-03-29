<?php

declare(strict_types=1);

namespace GearsDigital\WeAreOpen\Support;

/**
 * SiteYamlKeys
 *
 * Central definition of site.yml configuration keys
 * used by the WeAreOpen plugin.
 *
 * This class exists to:
 * - avoid magic strings
 * - provide a single refactoring point
 * - document expected site configuration keys
 *
 * Keys defined here represent *public configuration contracts*
 * between the site and the plugin.
 *
 * NOTE: Not final to allow PRO plugin extension.
 */
class SiteYamlKeys
{
    /**
     * Site configuration key containing weekly opening hours.
     *
     * Expected location: site.yml
     *
     * @example
     * openhours:
     *   mon:
     *     - "09:00-17:00"
     */
    public const string OPENHOURS = 'openhours';

    /**
     * Get all FREE plugin configuration keys.
     *
     * @return string[]
     */
    public static function all(): array
    {
        return [
            self::OPENHOURS,
        ];
    }
}
