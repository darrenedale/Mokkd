<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Integers;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;

/** The bounds are inclusive. */
class IsGreaterThan implements MatcherContract
{
    private int $lowerBound;

    public function __construct(int $lowerBound)
    {
        $this->lowerBound = $lowerBound;
    }

    public function matches(mixed $actual): bool
    {
        return is_int($actual) && $this->lowerBound < $actual;
    }

    public function describe(Serialiser $serialiser): string
    {
        return "A int greater than {$this->lowerBound}";
    }
}
