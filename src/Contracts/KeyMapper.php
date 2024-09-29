<?php

declare(strict_types=1);

namespace Mokkd\Contracts;

/**
 * Contract for classes that turn call arguments into keys for expectations that return using maps.
 *
 * Function mocks can be set up to return a value mapped from an associative array, based on the arguments provided
 * in the mocked function call. An instance of this interface will take the arguments and turn them into a key to use
 * to retrieve the return value from the associative array.
 */
interface KeyMapper
{
    /** Determine the key to use for a given set of arguments. */
    public function mapKey(mixed ...$args): string|int;
}
