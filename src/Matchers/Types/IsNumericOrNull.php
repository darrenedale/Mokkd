<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Types;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser as SerialiserContract;

/**
 * Matcher that requires any float or int value or null.
 *
 * This is type-safe - values that can ordinarily be coerced to ints/floats (e.g. strings containing int or float
 * values) *do not match*.
 */
class IsNumericOrNull implements MatcherContract
{
    public function matches(mixed $actual): bool
    {
        return null === $actual || is_float($actual) || is_int($actual);
    }

    public function describe(SerialiserContract $serialiser): string
    {
        return "(?int|float) {any}";
    }
}
