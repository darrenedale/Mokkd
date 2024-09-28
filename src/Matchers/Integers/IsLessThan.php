<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Integers;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;

/** The bounds are inclusive. */
class IsLessThan implements MatcherContract
{
    private int $upperBound;

    public function __construct(int $upperBound)
    {
        $this->upperBound = $upperBound;
    }

    public function matches(mixed $actual): bool
    {
        return is_int($actual) && $this->upperBound > $actual;
    }

    public function describe(Serialiser $serialiser): string
    {
        return "A int less than {$this->upperBound}";
    }
}
