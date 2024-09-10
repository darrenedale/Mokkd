<?php

declare(strict_types=1);

namespace Mokkd\Contracts;

/**
 * Contract for classes that turn call arguments into keys for expectations that return using maps.
 */
interface KeyMapper
{
    /** Determine the key to use. */
    public function mapKey(mixed ...$args): string|int;
}
