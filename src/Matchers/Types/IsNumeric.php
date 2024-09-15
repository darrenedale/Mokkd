<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Types;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser as SerialiserContract;

/** Matcher that requires any float or int value. */
class IsNumeric implements MatcherContract
{
    public function matches(mixed $actual): bool
    {
        return is_float($actual) || is_int($actual);
    }

    public function describe(SerialiserContract $serialiser): string
    {
        return "(float|int) {any}";
    }
}
