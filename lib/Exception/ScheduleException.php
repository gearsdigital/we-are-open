<?php

declare(strict_types=1);

namespace GearsDigital\WeAreOpen\Exception;

use RuntimeException;

/**
 * Base exception for all schedule-related errors.
 *
 * Provides a common exception type that can be caught to handle
 * any schedule system failure gracefully.
 */
class ScheduleException extends RuntimeException
{
}
