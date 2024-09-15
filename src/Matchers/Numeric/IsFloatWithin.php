<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Numeric;

use LogicException;
use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;

/** The bounds are inclusive. */
class IsFloatWithin implements MatcherContract
{
    private float $lowerBound;

    private float $upperBound;

    public function __construct(float $lowerBound, float $upperBound)
    {
        assert($lowerBound < $upperBound, new LogicException("Expected upper bound >= lower bound, found {$lowerBound} and {$upperBound}"));
        $this->lowerBound = $lowerBound;
        $this->upperBound = $upperBound;
    }

    public function matches(mixed $actual): bool
    {
        return is_float($actual) && $this->lowerBound < $actual && $actual < $this->upperBound;
    }

    public function describe(Serialiser $serialiser): string
    {
        return "A float between (but not equal to) {$this->lowerBound} and {$this->upperBound}";
    }
}
