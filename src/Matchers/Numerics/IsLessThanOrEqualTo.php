<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Numerics;

use LogicException;
use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;

/**
 * The bound is inclusive.
 *
 * TODO consider removing Numeric from the name as it's in the namespace
 */
class IsLessThanOrEqualTo implements MatcherContract
{
    private int|float $upperBound;

    public function __construct(int|float $upperBound)
    {
        $this->upperBound = $upperBound;
    }

    public function matches(mixed $actual): bool
    {
        if (!is_int($actual) && !is_float($actual)) {
            return false;
        }

        if (is_int($actual) && is_int($this->upperBound)) {
            return $this->upperBound >= $actual;
        }

        return (float) $this->upperBound >= (float) $actual;
    }

    public function describe(Serialiser $serialiser): string
    {
        return "A numeric value less than or equal to {$serialiser->serialise($this->upperBound)}";
    }
}
