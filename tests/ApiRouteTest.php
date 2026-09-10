<?php

declare(strict_types=1);

namespace GearsDigital\WeAreOpen\Tests;

use Kirby\Cms\App;

/**
 * Regression test for the Panel save route.
 *
 * openHoursEditorMixin.js calls `this.$api.patch("we-are-open/save", ...)`,
 * so the route has to accept PATCH — it was registered as POST, which made
 * every save in the Panel 404 before Api::save() ever ran.
 *
 * Reads the real index.php registration so it can't drift from what ships.
 */
final class ApiRouteTest extends \KirbyTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        require_once __DIR__ . '/../index.php';
    }

    private function route(string $pattern): array
    {
        $routes = App::plugin('gearsdigital/we-are-open')->extends()['api']['routes'];

        foreach ($routes as $route) {
            if ($route['pattern'] === $pattern) {
                return $route;
            }
        }

        $this->fail("No API route registered for pattern: {$pattern}");
    }

    public function test_save_route_accepts_the_patch_verb_the_panel_sends(): void
    {
        $this->assertSame('PATCH', $this->route('we-are-open/save')['method']);
    }

    public function test_load_route_stays_a_get(): void
    {
        $this->assertSame('GET', $this->route('we-are-open/load')['method']);
    }
}
