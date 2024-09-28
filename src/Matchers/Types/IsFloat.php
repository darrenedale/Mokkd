<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Types;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser as SerialiserContract;

/**
 * Matcher that requires the test value to be any float.
 */
class IsFloat implements MatcherContract
{
    public function matches(mixed $actual): bool
    {
        return is_float($actual);
    }

    public function describe(SerialiserContract $serialiser): string
    {
        return "(float) {any}";
    }
}
