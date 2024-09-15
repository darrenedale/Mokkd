<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Types;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser as SerialiserContract;

/** Matcher that requires any closed resource. */
class IsClosedResource implements MatcherContract
{
    public function matches(mixed $actual): bool
    {
        return "resource (closed)" === get_debug_type($actual);
    }

    public function describe(SerialiserContract $serialiser): string
    {
        return "(resource) {closed}";
    }
}
