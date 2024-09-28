<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Integers;

use LogicException;
use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;

/** The bounds are inclusive. */
class IsBetween implements MatcherContract
{
    private int $lowerBound;

    private int $upperBound;

    public function __construct(int $lowerBound, int $upperBound)
    {
        assert($lowerBound <= $upperBound, new LogicException("Expected upper bound >= lower bound, found {$lowerBound} and {$upperBound}"));
        $this->lowerBound = $lowerBound;
        $this->upperBound = $upperBound;
    }

    public function matches(mixed $actual): bool
    {
        return is_int($actual) && $this->lowerBound <= $actual && $actual <= $this->upperBound;
    }

    public function describe(Serialiser $serialiser): string
    {
        return "A int between {$this->lowerBound} and {$this->upperBound} inclusive";
    }
}
