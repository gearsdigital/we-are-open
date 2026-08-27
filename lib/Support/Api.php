<?php

declare(strict_types=1);

namespace GearsDigital\WeAreOpen\Support;

use Kirby\Data\Yaml;

final class Api
{
    public static function save(): array
    {
        $kirby = kirby();
        $data = $kirby->request()->body()->toArray();

        // FREE limitation:
        // - allow only ONE timeslot per weekday
        $openHours = $data['openHours'] ?? [];
        $openHours = self::normalizeOpenHours($openHours);

        // Use impersonate to ensure we have permissions
        $kirby->impersonate('kirby');
        $site = site();
        $site->update([
            'openhours' => Yaml::encode($openHours),
        ]);

        return [
            'status' => 'ok',
            'variant' => 'free',
        ];
    }

    public static function load(): array
    {
        // Ensure stored data respects FREE limitation (defensive)
        $openHours = site()->openhours()->yaml();
        $openHours = self::normalizeOpenHours($openHours);

        return [
            'openHours' => $openHours,
            'variant' => 'free',
        ];
    }

    /**
     * @param mixed $openHours
     * @return array<int, array{weekday: string, slots: array, isOpen: bool}>
     */
    public static function normalizeOpenHours(mixed $openHours): array
    {
        if (!is_array($openHours)) {
            return [];
        }

        $normalized = [];

        foreach ($openHours as $day) {
            if (!is_array($day)) {
                continue;
            }

            $weekday = $day['weekday'] ?? null;
            if (!is_string($weekday) || $weekday === '') {
                continue;
            }

            $slots = $day['slots'] ?? [];
            if (!is_array($slots)) {
                $slots = [];
            }

            // Keep only first slot (if any)
            $firstSlot = [];
            if (isset($slots[0]) && is_array($slots[0])) {
                $firstSlot = [
                    'start' => isset($slots[0]['start']) ? (string)$slots[0]['start'] : '',
                    'end' => isset($slots[0]['end']) ? (string)$slots[0]['end'] : '',
                ];

                // If both empty, treat as no slots
                if ($firstSlot['start'] === '' && $firstSlot['end'] === '') {
                    $firstSlot = [];
                } elseif ($firstSlot['start'] !== '' && $firstSlot['end'] !== '' && $firstSlot['start'] >= $firstSlot['end']) {
                    // Defense in depth: the panel already blocks saving an
                    // invalid range, but never persist one that gets through
                    // (a direct API call, a future client bug, ...).
                    $firstSlot = [];
                }
            }

            // Preserve isOpen state (default to true if not set)
            $isOpen = $day['isOpen'] ?? true;

            $normalized[] = [
                'weekday' => $weekday,
                'slots' => $firstSlot ? [$firstSlot] : [],
                'isOpen' => (bool)$isOpen,
            ];
        }

        return $normalized;
    }
}
