<?php

declare(strict_types=1);

use Kirby\Cms\App;
use PHPUnit\Framework\TestCase;

abstract class KirbyTestCase extends TestCase
{
    private App $baseApp;

    protected function setUp(): void
    {
        parent::setUp();
        $this->baseApp = App::instance();
    }

    protected function tearDown(): void
    {
        App::instance($this->baseApp);
        parent::tearDown();
    }

    /**
     * Clone the current Kirby instance with custom site content.
     * Content values must be raw field strings (YAML for structured fields).
     *
     * @param  array<string, string>  $content
     */
    protected function kirbyWithContent(array $content): App
    {
        return App::instance()->clone([
            'site' => ['content' => $content],
        ]);
    }

    /**
     * Encode a PHP array as a YAML string for use in site content.
     */
    protected function yaml(array $data): string
    {
        return Kirby\Data\Yaml::encode($data);
    }

    /**
     * Build a standard Mon–Fri 08:00–17:00 openhours model array.
     *
     * @return array<int, array{weekday: string, slots: array<int, array{start: string, end: string}>}>
     */
    protected function standardOpenHoursModel(): array
    {
        $weekdays = ['mon', 'tue', 'wed', 'thu', 'fri'];
        $model = [];
        foreach ($weekdays as $wd) {
            $model[] = [
                'weekday' => $wd,
                'slots'   => [['start' => '08:00:00', 'end' => '17:00:00']],
            ];
        }
        return $model;
    }
}
