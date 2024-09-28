<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Types;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser as SerialiserContract;

/** Matcher that requires any resource or null. */
class IsResourceOrNull implements MatcherContract
{
    public function matches(mixed $actual): bool
    {
        return null === $actual || is_resource($actual) || "resource (closed)" === get_debug_type($actual);
    }

    public function describe(SerialiserContract $serialiser): string
    {
        return "(?resource) {any}";
    }
}
