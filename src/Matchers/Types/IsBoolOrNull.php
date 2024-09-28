<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Types;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser as SerialiserContract;

/** Matcher that requires any boolean value or null. */
class IsBoolOrNull implements MatcherContract
{
    public function matches(mixed $actual): bool
    {
        return null === $actual || is_bool($actual);
    }

    public function describe(SerialiserContract $serialiser): string
    {
        return "(?bool) {any}";
    }
}
