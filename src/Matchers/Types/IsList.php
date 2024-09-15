<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Types;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser as SerialiserContract;

/** Matcher that requires a list array (numeric keys ascending in units from 0). */
class IsList implements MatcherContract
{
    public function matches(mixed $actual): bool
    {
        return is_array($actual) && array_is_list($actual);
    }

    public function describe(SerialiserContract $serialiser): string
    {
        return "(array) {list}";
    }
}
