<?php

declare(strict_types=1);

namespace GearsDigital\WeAreOpen\Tests\Support;

use GearsDigital\WeAreOpen\Support\Api;

/**
 * Regression tests for Api::normalizeOpenHours() server-side validation.
 *
 * The panel already blocks saving an invalid time range client-side, but
 * this is the defense-in-depth layer for anything that reaches the API
 * directly (a future client bug, a direct API call, ...).
 */
final class ApiTest extends \KirbyTestCase
{
    public function test_drops_a_slot_where_end_is_before_start(): void
    {
        $result = Api::normalizeOpenHours([
            ['weekday' => 'mon', 'slots' => [['start' => '17:00:00', 'end' => '08:00:00']], 'isOpen' => true],
        ]);

        $this->assertSame([], $result[0]['slots']);
    }

    public function test_drops_a_slot_where_end_equals_start(): void
    {
        $result = Api::normalizeOpenHours([
            ['weekday' => 'mon', 'slots' => [['start' => '08:00:00', 'end' => '08:00:00']], 'isOpen' => true],
        ]);

        $this->assertSame([], $result[0]['slots']);
    }

    public function test_keeps_a_valid_slot(): void
    {
        $result = Api::normalizeOpenHours([
            ['weekday' => 'mon', 'slots' => [['start' => '08:00:00', 'end' => '17:00:00']], 'isOpen' => true],
        ]);

        $this->assertSame([['start' => '08:00:00', 'end' => '17:00:00']], $result[0]['slots']);
    }
}
