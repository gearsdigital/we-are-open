<?php

declare(strict_types=1);

namespace GearsDigital\WeAreOpen\Exception;

/**
 * Thrown when plugin configuration is invalid.
 *
 * Examples:
 * - Holidays enabled but no country code set
 * - Invalid country code format
 * - Required fields missing
 */
class InvalidConfigurationException extends ScheduleException
{
}
