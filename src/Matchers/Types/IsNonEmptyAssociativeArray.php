<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Types;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser as SerialiserContract;
use Mokkd\Utilities\IterableAlgorithms;

/** Matcher that requires any associative array with at least one element. */
class IsNonEmptyAssociativeArray implements MatcherContract
{
    public function matches(mixed $actual): bool
    {
        return is_array($actual) && 0 < count($actual) && !array_is_list($actual);
    }

    public function describe(SerialiserContract $serialiser): string
    {
        return "(array) {non-empty-associative}";
    }
}
