<?php

declare(strict_types=1);

namespace GearsDigital\WeAreOpen\Tests\Support;

use GearsDigital\WeAreOpen\Support\TagOptionsParser;
use Kirby\Text\KirbyTag;

/**
 * Regression tests for TagOptionsParser <-> KirbyTag attribute matching.
 *
 * KirbyTag only promotes a registered attribute to $tag->{name} when
 * {name} is registered in lowercase (Kirby lowercases the incoming
 * attribute name before comparing, but does not lowercase the registered
 * list). Registering "showClosed"/"timeFormat" in camelCase silently
 * broke both attributes: the tag's own properties were never set,
 * regardless of what was written in the tag or how TagOptionsParser
 * read them back.
 */
final class TagOptionsParserTest extends \KirbyTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Mirrors the real registration in index.php.
        KirbyTag::$types['scheduletable'] = [
            'attr' => ['layout', 'showclosed', 'showweekends', 'timeformat', 'weekdayformat'],
        ];
    }

    private function parseTag(string $tagString): KirbyTag
    {
        return KirbyTag::parse($tagString);
    }

    public function test_show_closed_false_hides_closed_days(): void
    {
        $tag = $this->parseTag('(scheduleTable: showClosed: false)');
        $options = TagOptionsParser::parse($tag);

        $this->assertTrue($options['hideClosedDays']);
    }

    public function test_show_closed_true_shows_closed_days(): void
    {
        $tag = $this->parseTag('(scheduleTable: showClosed: true)');
        $options = TagOptionsParser::parse($tag);

        $this->assertFalse($options['hideClosedDays']);
    }

    public function test_result_uses_hide_closed_days_key_not_show_closed(): void
    {
        $tag = $this->parseTag('(scheduleTable: showClosed: false)');
        $options = TagOptionsParser::parse($tag);

        $this->assertArrayHasKey('hideClosedDays', $options);
        $this->assertArrayNotHasKey('showClosed', $options);
    }

    public function test_time_format_attribute_is_read(): void
    {
        $tag = $this->parseTag('(scheduleTable: timeFormat: H:i)');
        $options = TagOptionsParser::parse($tag);

        $this->assertSame('H:i', $options['timeFormat']);
    }

    public function test_omitted_attributes_fall_back_to_defaults(): void
    {
        $tag = $this->parseTag('(scheduleTable:)');
        $options = TagOptionsParser::parse($tag);

        // Site options 'showClosedDays' and 'showWeekends' both default to
        // true, i.e. closed days and weekends are shown by default.
        $this->assertFalse($options['hideClosedDays']);
        $this->assertFalse($options['hideWeekends']);
        $this->assertNull($options['timeFormat']);
    }

    public function test_show_weekends_false_hides_weekends(): void
    {
        $tag = $this->parseTag('(scheduleTable: showWeekends: false)');
        $options = TagOptionsParser::parse($tag);

        $this->assertTrue($options['hideWeekends']);
    }

    public function test_show_weekends_true_shows_weekends(): void
    {
        $tag = $this->parseTag('(scheduleTable: showWeekends: true)');
        $options = TagOptionsParser::parse($tag);

        $this->assertFalse($options['hideWeekends']);
    }

    /**
     * @dataProvider weekdayFormatLongSynonyms
     */
    public function test_weekday_format_long_synonyms_map_to_l(string $value): void
    {
        $tag = $this->parseTag("(scheduleTable: weekdayFormat: {$value})");
        $options = TagOptionsParser::parse($tag);

        $this->assertSame('l', $options['weekdayFormat']);
    }

    public static function weekdayFormatLongSynonyms(): array
    {
        return [['l'], ['L'], ['long'], ['Long'], ['full']];
    }

    public function test_weekday_format_short_defaults_to_null(): void
    {
        $tag = $this->parseTag('(scheduleTable:)');
        $options = TagOptionsParser::parse($tag);

        $this->assertNull($options['weekdayFormat']);
    }

    public function test_weekday_format_unrecognized_value_falls_back_to_null(): void
    {
        $tag = $this->parseTag('(scheduleTable: weekdayFormat: banana)');
        $options = TagOptionsParser::parse($tag);

        $this->assertNull($options['weekdayFormat']);
    }
}
