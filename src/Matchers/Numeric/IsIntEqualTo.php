<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Numeric;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;

class IsIntEqualTo implements MatcherContract
{
    private int $expected;

    public function __construct(int $expected)
    {
        $this->expected = $expected;
    }

    public function matches(mixed $actual): bool
    {
        return $this->expected === $actual;
    }

    public function describe(Serialiser $serialiser): string
    {
        return "A int equal to {$this->expected}";
    }
}
