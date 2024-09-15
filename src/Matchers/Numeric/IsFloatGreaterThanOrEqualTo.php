<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Numeric;

use LogicException;
use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;

/** The bounds are inclusive. */
class IsFloatGreaterThanOrEqualTo implements MatcherContract
{
    private float $lowerBound;

    public function __construct(float $lowerBound)
    {
        $this->lowerBound = $lowerBound;
    }

    public function matches(mixed $actual): bool
    {
        return is_float($actual) && $this->lowerBound <= $actual;
    }

    public function describe(Serialiser $serialiser): string
    {
        return "A float greater than or equal to {$this->lowerBound}";
    }
}
