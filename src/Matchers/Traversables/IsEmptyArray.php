<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Traversables;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser as SerialiserContract;

/** Matcher that requires any array with no elements. */
class IsEmptyArray implements MatcherContract
{
    public function matches(mixed $actual): bool
    {
        return is_array($actual) && 0 === count($actual);
    }

    public function describe(SerialiserContract $serialiser): string
    {
        return "(array) {empty}";
    }
}
