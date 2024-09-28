<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Types;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser as SerialiserContract;

/**
 * Matcher that requires the test value to be any float or int.
 *
 * This is type-safe - values that can ordinarily be coerced to ints/floats (e.g. strings containing int or float
 * values) *do not match*.
 */
class IsNumeric implements MatcherContract
{
    public function matches(mixed $actual): bool
    {
        return is_float($actual) || is_int($actual);
    }

    public function describe(SerialiserContract $serialiser): string
    {
        return "(int|float) {any}";
    }
}
