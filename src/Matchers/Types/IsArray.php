<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Types;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser as SerialiserContract;

/** Matcher that requires any array value. */
class IsArray implements MatcherContract
{
    public function matches(mixed $actual): bool
    {
        return is_array($actual);
    }

    public function describe(SerialiserContract $serialiser): string
    {
        return "(array) {any}";
    }
}
