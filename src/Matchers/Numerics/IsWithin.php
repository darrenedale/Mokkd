<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Numerics;

use LogicException;
use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;

/**
 * The bounds are exclusive.
 *
 * TODO consider removing Numeric from the name as it's in the namespace
 */
class IsWithin implements MatcherContract
{
    private int|float $lowerBound;

    private int|float $upperBound;

    private bool $canUseIntegralComparison;

    public function __construct(int|float $lowerBound, int|float $upperBound)
    {
        assert($lowerBound < $upperBound, new LogicException("Expected upper bound > lower bound, found {$lowerBound} and {$upperBound}"));
        $this->lowerBound = $lowerBound;
        $this->upperBound = $upperBound;
        $this->canUseIntegralComparison = is_int($lowerBound) && is_int($upperBound);
    }

    public function matches(mixed $actual): bool
    {
        if (!is_int($actual) && !is_float($actual)) {
            return false;
        }

        if (is_int($actual) && $this->canUseIntegralComparison) {
            return $this->lowerBound < $actual && $actual < $this->upperBound;
        }

        $actual = (float) $actual;
        return (float) $this->lowerBound < $actual && $actual < (float) $this->upperBound;
    }

    public function describe(Serialiser $serialiser): string
    {
        return "A numeric value between (but not equal to) {$serialiser->serialise($this->lowerBound)} and {$serialiser->serialise($this->upperBound)}";
    }
}
