<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Numerics;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;

class IsMultipleOf implements MatcherContract
{
    private int|float $expected;

    public function __construct(int|float $expected)
    {
        $this->expected = $expected;
    }

    public function matches(mixed $actual): bool
    {
        if (is_int($actual) && is_int($this->expected)) {
            return 0 === $actual % $this->expected;
        }

        $actual = (float) $actual;
        $expected = (float) $this->expected;

        return 0.0 === ($actual / $expected) - (floor($actual / $expected));
    }

    public function describe(Serialiser $serialiser): string
    {
        return "A float that's a multiple of {$this->expected}";
    }
}
