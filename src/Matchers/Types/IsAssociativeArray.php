<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Types;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser as SerialiserContract;
use Mokkd\Utilities\IterableAlgorithms;

/**
 * Matcher that requires the test value to be any associative array.
 *
 * An associative array has keys that are:
 * - strings; or
 * - a mixture of strings and ints; or
 * - non-consecutive ints; or
 * - consecutive ints that don't begin at 0
 *
 * Note that an empty array qualifies as an associative array.
 */
class IsAssociativeArray implements MatcherContract
{
    public function matches(mixed $actual): bool
    {
        return is_array($actual) && (0 === count($actual) || !array_is_list($actual));
    }

    public function describe(SerialiserContract $serialiser): string
    {
        return "(array) {associative}";
    }
}
