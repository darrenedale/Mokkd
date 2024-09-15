<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Types;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser as SerialiserContract;

/** Matcher that requires any string value. */
class IsString implements MatcherContract
{
    public function matches(mixed $actual): bool
    {
        return is_string($actual);
    }

    public function describe(SerialiserContract $serialiser): string
    {
        return "(string) {any}";
    }
}
