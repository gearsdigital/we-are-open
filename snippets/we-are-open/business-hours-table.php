<?php
/**
 * Kirby snippet: we-are-open/business-hours-table.php (FREE version)
 *
 * Displays a simple flat list of opening hours.
 *
 * Provided variables:
 * - $tableData: BusinessHoursDay[] list (flat, one per weekday)
 * - $options:   normalized options used for building (optional)
 * - $tag:       KirbyTag (optional)
 *
 * NOTE: FREE version does NOT receive $rawData (PRO-only feature)
 *
 * Rendering rule:
 * - $day->label for the left column
 * - $day->formattedSlots for the right column
 * - $day->isClosed controls placeholder output
 */

$tableData = $tableData ?? [];
$options = $options ?? [];
$tag = $tag ?? null;

if (!is_array($tableData) || empty($tableData)) {
    return;
}

// Presentation defaults (override by copying this snippet into your site/snippets if needed)
$closedPlaceholder = '–';
$rangeSeparator = '–';
$slotJoin = ', ';

// Optional CSS hook based on tag layout
$layoutClass = '';
if (is_object($tag) && isset($tag->layout) && $tag->layout !== null) {
    $layoutClass = ' is-layout-'.esc(strtolower((string)$tag->layout));
}

?>
<table class="we-are-open-table<?= $layoutClass ?>">
  <tbody>
  <?php
  foreach ($tableData as $day): ?>
    <?php
    // Direct property access (BusinessHoursDay objects)
    $label = (string)$day->label;
    $formattedSlots = $day->formattedSlots ?? [];
    $isClosed = empty($formattedSlots);

    // Build "7:30–10:25, 15:00–19:00"
    $parts = [];
    foreach ($formattedSlots as $slot) {
        if (!is_object($slot) || !property_exists($slot, 'start') || !property_exists($slot, 'end')) {
            continue;
        }

        $start = (string)$slot->start;
        $end = (string)$slot->end;

        if ($start === '' || $end === '') {
            continue;
        }

        $parts[] = $start.$rangeSeparator.$end;
    }

    // If open but slots are somehow empty, still fall back to placeholder.
    $hoursText = $isClosed ? $closedPlaceholder : (empty($parts) ? $closedPlaceholder : implode($slotJoin, $parts));
    ?>
    <tr class="we-are-open-row<?= $isClosed ? ' is-closed' : '' ?>">
      <th scope="row" class="we-are-open-day"><?= esc($label) ?></th>
      <td class="we-are-open-hours"><?= esc($hoursText) ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
