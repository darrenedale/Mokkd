<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Numeric;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;

/** Comparing floating point values for equality is subject to precision errors. */
class IsFloatEqualTo implements MatcherContract
{
    private float $expected;

    public function __construct(float $expected)
    {
        $this->expected = $expected;
    }

    public function matches(mixed $actual): bool
    {
        return $this->expected === $actual;
    }

    public function describe(Serialiser $serialiser): string
    {
        return "A float equal to {$this->expected}";
    }
}
