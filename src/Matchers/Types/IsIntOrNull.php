<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Types;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser as SerialiserContract;

/** Matcher that requires any int value or null. */
class IsIntOrNull implements MatcherContract
{
    public function matches(mixed $actual): bool
    {
        return null === $actual || is_int($actual);
    }

    public function describe(SerialiserContract $serialiser): string
    {
        return "(?int) {any}";
    }
}
