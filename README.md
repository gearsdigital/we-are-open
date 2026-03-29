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
(scheduleTable: showClosed: true showWeekends: true)
```

**Options**

| Option | Default | Description |
|--------|---------|-------------|
| `layout` | `table` | `table` or `list` |
| `showClosed` | `false` | Show days marked as closed |
| `showWeekends` | `false` | Show Saturday and Sunday |
| `weekdayFormat` | `long` | `long`, `short`, or `narrow` |
| `timeFormat` | `short` | `short` or `medium` |
| `grouping` | `false` | Group consecutive days with identical hours |
| `groupMinSize` | `2` | Minimum days to form a group |
| `groupDaySeparator` | `–` | Separator between grouped day names |

## Snippets

| Snippet | Description |
|---------|-------------|
| `we-are-open/business-hours-table` | Weekly hours table |

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
