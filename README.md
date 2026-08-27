# We Are Open

Opening hours change more often than most sites make it easy to update – usually that means building a custom panel field just to manage them, then hand-rolling the template logic to display them. **We Are Open** gives you both out of the box: set the week's hours in a dedicated panel screen, drop `(scheduleTable:)` into a template, and it just renders – you just need to add some CSS to make it fit your site.

![The Regular Opening Hours screen in the Kirby panel](media/panel-screenshot.png)

**Free** covers the everyday case: one time slot per weekday. **[We Are Open PRO](#we-are-open-pro)** adds multiple slots per day, exception days, and public holidays that keep themselves up to date. It's early access – [request it by email](mailto:plugins@gearsdigital.com).

## Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Documentation](#documentation)
  - [Site method](#site-method)
  - [KirbyTags](#kirbytags)
  - [Snippets](#snippets)
- [We Are Open PRO](#we-are-open-pro)
- [Development](#development)
- [License](#license)

## Requirements

- Kirby CMS >= 4.0
- PHP >= 8.3

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

## Documentation

### Site method

#### `site()->weAreOpen()`

Returns a `WeAreOpenFacade` instance for accessing opening hours in templates and snippets.

##### `->businessHours()`

Returns this week's opening hours as an array of `BusinessHoursDay` objects, one per weekday (Mon–Sun).

```php
$hours = site()->weAreOpen()->businessHours();

foreach ($hours as $day) {
    echo $day->label; // "Monday"
    foreach ($day->slots as $slot) {
        echo $slot->start . '–' . $slot->end; // "08:00:00–17:00:00"
    }
}
```

See [Snippets](#snippets) for all available properties on `BusinessHoursDay` and `TimeRange`.

### KirbyTags

#### `(scheduleTable:)`

Renders a formatted table of your weekly opening hours.

```
(scheduleTable:)
(scheduleTable: showClosed: true timeFormat: H:i)
```

**Options**

| Option | Default | Description |
|--------|---------|-------------|
| `layout` | – | CSS class added as `is-layout-{value}` on the table element. Pass any string – no functional effect in the Free version. |
| `showClosed` | `true` | Include days marked as closed in the output |
| `showWeekends` | `true` | Include Saturday and Sunday in the output |
| `timeFormat` | `G:i` | PHP [`date()`](https://www.php.net/manual/en/function.date.php) format string for time output – e.g. `H:i` (09:00), `g:ia` (9:00am) |
| `weekdayFormat` | short | `long`/`l`/`full` for full weekday names (e.g. `Monday`); anything else keeps the short form (e.g. `Mon`) |

**Example output** – for a week configured as Mon–Fri 8:00–17:00 (Thu until
19:00) and Sat 9:00–13:00, Sunday closed:

```
(scheduleTable:)

Mon   8:00–17:00
Tue   8:00–17:00
Wed   8:00–17:00
Thu   8:00–19:00
Fri   8:00–17:00
Sat   9:00–13:00
Sun   –
```

```
(scheduleTable: showClosed: false)

Mon   8:00–17:00
Tue   8:00–17:00
Wed   8:00–17:00
Thu   8:00–19:00
Fri   8:00–17:00
Sat   9:00–13:00
```

```
(scheduleTable: showWeekends: false)

Mon   8:00–17:00
Tue   8:00–17:00
Wed   8:00–17:00
Thu   8:00–19:00
Fri   8:00–17:00
```

```
(scheduleTable: timeFormat: H:i)

Mon   08:00–17:00
...
Sat   09:00–13:00
Sun   –
```

```
(scheduleTable: timeFormat: g:ia)

Mon   8:00am–5:00pm
...
Sat   9:00am–1:00pm
Sun   –
```

```
(scheduleTable: weekdayFormat: long)

Monday      8:00–17:00
Tuesday     8:00–17:00
Wednesday   8:00–17:00
Thursday    8:00–19:00
Friday      8:00–17:00
Saturday    9:00–13:00
Sunday      –
```

### Snippets

Kirby resolves snippets from `site/snippets/` before falling back to plugin-provided ones. To override any snippet, copy the file from the plugin into the matching path under `site/snippets/` and edit it freely – plugin updates will never overwrite your version.

```
site/snippets/
└── we-are-open/
    └── business-hours-table.php   ← your custom version
```

#### `we-are-open/business-hours-table`

Renders the `(scheduleTable:)` output.

**Variables**

| Variable | Type | Description |
|----------|------|-------------|
| `$tableData` | `BusinessHoursDay[]` | One entry per weekday – see properties below |
| `$options` | `array` | Normalised options passed to the tag |
| `$tag` | `KirbyTag\|null` | The originating KirbyTag instance |

**`BusinessHoursDay` properties**

| Property | Type | Description |
|----------|------|-------------|
| `$day->label` | `string` | Localised weekday name (e.g. `Monday`) |
| `$day->slots` | `TimeRange[]` | Raw time slots as stored |
| `$day->formattedSlots` | `TimeRange[]` | Time slots formatted according to `timeFormat` option |
| `$day->weekday` | `Weekday` | Weekday value object – cast to string for `mon`–`sun` |

**`TimeRange` properties** (each item in `slots` / `formattedSlots`)

| Property | Type | Description |
|----------|------|-------------|
| `$slot->start` | `string` | Start time (e.g. `09:00`) |
| `$slot->end` | `string` | End time (e.g. `17:00`) |

**Formatting tips**

Use `$day->formattedSlots` for times already formatted by the `timeFormat` option. Use `$day->slots` for the raw `HH:MM:SS` value to apply your own format:

```php
// Custom time format in a snippet
$start = date('H:i', strtotime($slot->start)); // → "09:00"
$start = date('g:ia', strtotime($slot->start)); // → "9:00am"
```

The weekday is available as a two-letter code (`mon`–`sun`) via `$day->weekday`. Use it with PHP's `date()` to get a localised name:

```php
// Custom weekday label in a snippet
$label = date('l', strtotime('next ' . $day->weekday)); // → "Monday"
$label = date('D', strtotime('next ' . $day->weekday));  // → "Mon"
```

For localised names that respect the site language, `$day->label` already contains the correct translation.

## We Are Open PRO

Free covers most single-location schedules well. **We Are Open PRO** is for
the cases that don't fit a single time slot per day: lunch breaks, split
shifts, seasonal closures, and public holidays that shouldn't need a yearly
reminder.

- **Multiple time slots per day** – split a day into two or more open
  windows, e.g. 9:00–12:00 and 14:00–18:00 for a lunch break, instead of one
  continuous slot.
- **Exception days** – override the regular schedule for a single date: a
  training day, a spontaneous closure, a one-off late opening.
- **Exception day ranges** – the same override, spanning a start and end
  date: company holidays, renovations, seasonal closures.
- **Public holiday detection** – pick a country and public holidays are
  added automatically, kept up to date every year. Any holiday can still
  get its own hours if you're open anyway.
- **`(openNote:)` KirbyTag** – a short, self-updating status line (open,
  closed, or opening soon) for headers, banners, or a contact page.
- **Grouped days with identical hours** – "Mon–Fri 8:00–17:00" instead of
  five separate rows, wherever consecutive days share the same schedule.
- **Extended `scheduleTable` options** – more control over the rendered
  table beyond the Free set.

**For snippet authors**, Pro's `we-are-open/business-hours-table` snippet
also gets:

- **`$rawData`** – the underlying model behind `$tableData`, for building a
  fully custom rendering beyond what `BusinessHoursDay` exposes.
- **`$day->isClosed` / `$day->isWeekend`** – Free snippets derive "closed"
  from empty slots themselves; Pro's `BusinessHoursDay` carries both flags
  directly.

| Feature | Free | Pro |
|---------|:----:|:---:|
| Regular opening hours | ✅ | ✅ |
| One time slot per day | ✅ | ✅ |
| `(scheduleTable:)` KirbyTag | ✅ | ✅ |
| Multiple time slots per day | – | ✅ |
| Exception days | – | ✅ |
| Exception day ranges | – | ✅ |
| Grouped days with identical hours | – | ✅ |
| Public holiday detection | – | ✅ |
| `(openNote:)` KirbyTag | – | ✅ |
| Extended `scheduleTable` options | – | ✅ |

Every feature above is ready to use today – multiple slots, exceptions,
holidays, the lot. It's just not on Packagist yet: email
[plugins@gearsdigital.com](mailto:plugins@gearsdigital.com) to get it.

## Development

```bash
# Development with watch mode
npm run dev

# Production build
npm run build
```

## License

[MIT](LICENSE)
