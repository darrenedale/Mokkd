<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Types;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser as SerialiserContract;
use Mokkd\Utilities\IterableAlgorithms;

/**
 * Matcher that requires null or any associative array whose keys are all strings.
 *
 * Note that an empty array qualifies as a property map.
 */
class IsPropertyMapOrNull implements MatcherContract
{
    public function matches(mixed $actual): bool
    {
        return null === $actual || (is_array($actual) && IterableAlgorithms::allKeys($actual, "is_string"));
    }

    public function describe(SerialiserContract $serialiser): string
    {
        return "(?array) {property-map}";
    }
}
