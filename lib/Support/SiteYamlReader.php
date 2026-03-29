<?php

declare(strict_types=1);

namespace GearsDigital\WeAreOpen\Support;

use Throwable;

final class SiteYamlReader
{
    /**
     * @return array<string, mixed>
     */
    public static function get(string $fieldName): array
    {
        try {
            $site = site();
            if (!method_exists($site, 'content')) {
                return [];
            }

            $field = $site->content()->get($fieldName);
            if ($field->isEmpty()) {
                return [];
            }

            $yaml = $field->yaml();

            return is_array($yaml) ? $yaml : [];
        } catch (Throwable $e) {
            return [];
        }
    }
}
