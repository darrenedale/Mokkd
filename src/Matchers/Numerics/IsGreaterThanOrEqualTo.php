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
class IsGreaterThanOrEqualTo implements MatcherContract
{
    private int|float $lowerBound;

    public function __construct(int|float $lowerBound)
    {
        $this->lowerBound = $lowerBound;
    }

    public function matches(mixed $actual): bool
    {
        if (!is_int($actual) && !is_float($actual)) {
            return false;
        }

        if (is_int($actual) && is_int($this->lowerBound)) {
            return $this->lowerBound <= $actual;
        }

        return (float) $this->lowerBound <= (float) $actual;
    }

    public function describe(Serialiser $serialiser): string
    {
        return "A numeric value greater than or equal to {$serialiser->serialise($this->lowerBound)}";
    }
}
