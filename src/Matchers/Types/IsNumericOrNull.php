<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Types;

use Mokkd\Contracts\Serialiser as SerialiserContract;
use Mokkd\Matchers\Composite\MatchesAnyOf;

/**
 * Matcher that requires the test value to be any float or int, or null.
 *
 * This is type-safe - values that can ordinarily be coerced to ints/floats (e.g. strings containing int or float
 * values) *do not match*.
 */
class IsNumericOrNull extends MatchesAnyOf
{
    public function __construct()
    {
        parent::__construct(new IsNull(), new IsNumeric());
    }

    public function describe(SerialiserContract $serialiser): string
    {
        return "(?int|float) {any}";
    }
}
