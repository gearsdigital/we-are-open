<?php

declare(strict_types=1);

namespace GearsDigital\WeAreOpen\Tests;

use Kirby\Cms\Section;

/**
 * Regression tests for the 'openinghours' panel section — the extension
 * point that lets We Are Open be embedded in any blueprint (site.yml, a
 * page, ...) instead of only living in its own dedicated view.
 *
 * Loads the real index.php registration (rather than duplicating its
 * closures here) so this can't silently drift from what actually ships.
 */
final class SectionTest extends \KirbyTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        require_once __DIR__ . '/../index.php';
    }

    private function section(?array $model = null, array $attrs = []): Section
    {
        $model ??= $this->standardOpenHoursModel();
        \Kirby\Cms\App::instance($this->kirbyWithContent(['openhours' => $this->yaml($model)]));

        return new Section('openinghours', ['model' => site(), ...$attrs]);
    }

    public function test_default_label_is_translated_title(): void
    {
        $section = $this->section();
        $this->assertSame('Opening hours', $section->toArray()['label']);
    }

    public function test_label_can_be_overridden(): void
    {
        $section = $this->section(attrs: ['label' => 'Business Hours']);
        $this->assertSame('Business Hours', $section->toArray()['label']);
    }

    public function test_exposes_default_start_and_end_time(): void
    {
        $section = $this->section();
        $data = $section->toArray();

        $this->assertSame('08:00:00', $data['defaultStartTime']);
        $this->assertSame('17:00:00', $data['defaultEndTime']);
    }

    public function test_exposes_open_hours_from_site_content(): void
    {
        $section = $this->section($this->standardOpenHoursModel());
        $data = $section->toArray();

        $this->assertCount(5, $data['openHours']);
        $this->assertSame('mon', $data['openHours'][0]['weekday']);
    }

    public function test_empty_site_content_yields_empty_open_hours(): void
    {
        $section = $this->section([]);
        $this->assertSame([], $section->toArray()['openHours']);
    }
}
