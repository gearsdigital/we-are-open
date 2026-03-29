# We Are Open

A Kirby panel plugin for managing regular opening hours — straight from the panel, no YAML editing required.

## Requirements

- Kirby CMS >= 4.0
- PHP >= 8.2

## Installation

### Composer

```bash
composer require gearsdigital/we-are-open
```

### Manual

Download and copy this repository to `/site/plugins/we-are-open`.

## Configuration

```php
// config/config.php
return [
    'gearsdigital.we-are-open.defaultStartTime' => '08:00:00',
    'gearsdigital.we-are-open.defaultEndTime'   => '17:00:00',
    'gearsdigital.we-are-open.timezone'         => 'Europe/Berlin',
];
```

## KirbyTags

### `(scheduleTable:)`

Renders a formatted table of your weekly opening hours.

```
(scheduleTable:)
(scheduleTable: showClosed: true)
```

**Options**

| Option | Default | Description |
|--------|---------|-------------|
| `layout` | — | Optional CSS class hook added as `is-layout-{value}` on the table |
| `showClosed` | `false` | Include days marked as closed in the output |

## Snippets

Kirby resolves snippets from `site/snippets/` before falling back to plugin-provided ones. To override any snippet, copy the file from the plugin into the matching path under `site/snippets/` and edit it freely — plugin updates will never overwrite your version.

```
site/snippets/
└── we-are-open/
    └── business-hours-table.php   ← your custom version
```

### `we-are-open/business-hours-table`

Renders the `(scheduleTable:)` output.

**Variables**

| Variable | Type | Description |
|----------|------|-------------|
| `$tableData` | `BusinessHoursDay[]` | One entry per weekday — see properties below |
| `$options` | `array` | Normalised options passed to the tag |
| `$tag` | `KirbyTag\|null` | The originating KirbyTag instance |

**`BusinessHoursDay` properties**

| Property | Type | Description |
|----------|------|-------------|
| `$day->label` | `string` | Localised weekday name (e.g. `Monday`) |
| `$day->isClosed` | `bool` | Whether this day has no opening hours |
| `$day->isWeekend` | `bool` | Whether this day is Saturday or Sunday |
| `$day->slots` | `TimeRange[]` | Raw time slots as stored |
| `$day->formattedSlots` | `TimeRange[]` | Time slots formatted according to `timeFormat` option |
| `$day->weekday` | `Weekday` | Weekday value object — cast to string for `mon`–`sun` |

**`TimeRange` properties** (each item in `slots` / `formattedSlots`)

| Property | Type | Description |
|----------|------|-------------|
| `$slot->start` | `string` | Start time (e.g. `09:00`) |
| `$slot->end` | `string` | End time (e.g. `17:00`) |

## Free vs. Pro

| Feature | Free | Pro |
|---------|:----:|:---:|
| Regular opening hours | ✅ | ✅ |
| One time slot per day | ✅ | ✅ |
| `(scheduleTable:)` KirbyTag | ✅ | ✅ |
| Multiple time slots per day | — | ✅ |
| Exception days | — | ✅ |
| Exception day ranges | — | ✅ |
| Public holiday detection | — | ✅ |
| `(openNote:)` KirbyTag | — | ✅ |
| Extended `scheduleTable` options | — | ✅ |

[→ We Are Open PRO](https://github.com/gearsdigital/we-are-open-pro)

## Development

```bash
# Development with watch mode
npm run dev

# Production build
npm run build
```

## License

[MIT](LICENSE)
