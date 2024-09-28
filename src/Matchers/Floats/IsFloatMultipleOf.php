<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Floats;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;

class IsFloatMultipleOf implements MatcherContract
{
    private float $expected;

    public function __construct(float $expected)
    {
        $this->expected = $expected;
    }

    public function matches(mixed $actual): bool
    {
        if (!is_float($actual)) {
            return false;
        }

        return 0.0 === ($actual / $this->expected) - (floor($actual / $this->expected));
    }

    public function describe(Serialiser $serialiser): string
    {
        return "A float that's a multiple of {$this->expected}";
    }
}
