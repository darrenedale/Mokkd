<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Types;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser as SerialiserContract;

/** Matcher that requires any object. */
class IsObject implements MatcherContract
{
    public function matches(mixed $actual): bool
    {
        return is_object($actual);
    }

    public function describe(SerialiserContract $serialiser): string
    {
        return "(object) {any}";
    }
}
