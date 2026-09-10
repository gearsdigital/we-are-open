<?php

declare(strict_types=1);

namespace GearsDigital\WeAreOpen\Tests\Facade;

use GearsDigital\WeAreOpen\Facade\WeAreOpenFacade;

/**
 * Regression tests for WeAreOpenFacade::businessHours() matching its
 * documented contract in README.md ("one per weekday, Mon–Sun",
 * `$day->label` e.g. "Monday") rather than BusinessHoursListService's
 * own defaults (5 weekdays, short labels — tuned for the scheduleTable
 * tag instead).
 */
final class WeAreOpenFacadeTest extends \KirbyTestCase
{
    private function facadeWith(array $model): WeAreOpenFacade
    {
        \Kirby\Cms\App::instance($this->kirbyWithContent(['openhours' => $this->yaml($model)]));

        return new WeAreOpenFacade();
    }

    public function test_returns_all_seven_weekdays(): void
    {
        $facade = $this->facadeWith($this->standardOpenHoursModel());
        $days = $facade->businessHours();

        $weekdays = array_map(fn ($d) => (string) $d->weekday, $days);
        $this->assertSame(['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'], $weekdays);
    }

    public function test_uses_long_weekday_labels(): void
    {
        $facade = $this->facadeWith($this->standardOpenHoursModel());
        $days = $facade->businessHours();

        // Long form ("Monday"), not short ("Mon"/BusinessHoursListService's
        // own default weekdayFormat 'D'). With no site language and no
        // configured locale the fallback is locale-neutral English, per
        // BusinessHoursListOptions::fromArray — the point of this test is
        // the format, not the language.
        $this->assertSame('Monday', $days[0]->label);
    }

    public function test_weekday_labels_honor_configured_locale(): void
    {
        \Kirby\Cms\App::instance($this->kirbyWithContent(['openhours' => $this->yaml($this->standardOpenHoursModel())])->clone([
            'options' => ['gearsdigital.we-are-open.locale' => 'de_DE'],
        ]));

        $days = (new WeAreOpenFacade())->businessHours();

        $this->assertSame('Montag', $days[0]->label);
    }
}
