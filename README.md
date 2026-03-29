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

### `we-are-open/business-hours-table`

Renders the `(scheduleTable:)` output. Copy it into `site/snippets/we-are-open/business-hours-table.php` to customise the HTML.

**Variables**

| Variable | Type | Description |
|----------|------|-------------|
| `$tableData` | `BusinessHoursDay[]` | One entry per weekday with `label`, `isClosed`, and `formattedSlots` |
| `$options` | `array` | Normalised options passed to the tag |
| `$tag` | `KirbyTag\|null` | The originating KirbyTag instance |

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
