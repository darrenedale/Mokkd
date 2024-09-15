<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Numeric;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;

class IsFloatNotEqualTo implements MatcherContract
{
    private float $expected;

    public function __construct(float $expected)
    {
        $this->expected = $expected;
    }

    public function matches(mixed $actual): bool
    {
        return is_float($actual) && $this->expected != $actual;
    }

    public function describe(Serialiser $serialiser): string
    {
        return "A float not equal to {$this->expected}";
    }
}
