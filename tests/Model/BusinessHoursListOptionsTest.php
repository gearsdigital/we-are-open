<?php

declare(strict_types=1);

namespace GearsDigital\WeAreOpen\Tests\Model;

use GearsDigital\WeAreOpen\Model\BusinessHoursListOptions;

/**
 * Regression tests for BusinessHoursListOptions::fromArray() defaults.
 *
 * The locale fallback used to be a hardcoded 'de_DE', which silently
 * rendered German weekday names on any single-language install (no
 * site/languages/*.php, so kirby()->language() is null).
 */
final class BusinessHoursListOptionsTest extends \KirbyTestCase
{
    public function test_locale_falls_back_to_neutral_english_when_none_given(): void
    {
        $options = BusinessHoursListOptions::fromArray([]);

        $this->assertSame('en', $options->locale);
    }

    public function test_explicit_locale_is_kept(): void
    {
        $options = BusinessHoursListOptions::fromArray(['locale' => 'fr_FR']);

        $this->assertSame('fr_FR', $options->locale);
    }

    public function test_falls_back_to_kirby_locale_config_stripped_of_charset(): void
    {
        \Kirby\Cms\App::instance()->clone(['options' => ['locale' => 'de_DE.UTF-8']]);

        $options = BusinessHoursListOptions::fromArray([]);

        $this->assertSame('de_DE', $options->locale);
    }

    public function test_reads_lc_time_from_an_array_locale_config(): void
    {
        \Kirby\Cms\App::instance()->clone(['options' => ['locale' => [LC_TIME => 'fr_FR.UTF-8']]]);

        $options = BusinessHoursListOptions::fromArray([]);

        $this->assertSame('fr_FR', $options->locale);
    }
}
