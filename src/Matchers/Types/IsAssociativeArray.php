<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Types;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser as SerialiserContract;
use Mokkd\Utilities\IterableAlgorithms;

/**
 * Matcher that requires an associative array (string or mixed keys, non-consecutive int keys or consecutive int keys
 * that don't begin at 0).
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
