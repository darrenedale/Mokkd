<?php

declare(strict_types=1);

namespace Mokkd\Matchers;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser as SerialiserContract;

/** An argument matcher that accepts anything. */
class Any implements MatcherContract
{

    public function __construct()
    {
    }

    public function matches(mixed $actual): bool
    {
        return true;
    }

    public function describe(SerialiserContract $serialiser): string
    {
        return "{any}";
    }
}
