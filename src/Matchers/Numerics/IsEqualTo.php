<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Numerics;

use LogicException;
use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;

/**
 * Comparing floating point values for equality is subject to precision errors.
 *
 * TODO consider removing Numeric from the name as it's in the namespace
 */
class IsEqualTo implements MatcherContract
{
    private int|float $expected;

    public function __construct(int|float $expected)
    {
        $this->expected = $expected;
    }

    public function matches(mixed $actual): bool
    {
        if (!is_int($actual) && !is_float($actual)) {
            return false;
        }

        if (is_int($actual) && is_int($this->expected)) {
            return $this->expected == $actual;
        }

        return (float) $this->expected == (float) $actual;
    }

    public function describe(Serialiser $serialiser): string
    {
        return "A numeric value equal to {$serialiser->serialise($this->expected)}";
    }
}
