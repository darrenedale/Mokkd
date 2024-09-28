<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Types;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser as SerialiserContract;

/**
 * Matcher that requires a list array (numeric keys ascending in units from 0) or null.
 *
 * Note that an empty array qualifies as a list array.
 */
class IsListOrNull implements MatcherContract
{
    public function matches(mixed $actual): bool
    {
        return null === $actual || (is_array($actual) && array_is_list($actual));
    }

    public function describe(SerialiserContract $serialiser): string
    {
        return "(?array) {list}";
    }
}
