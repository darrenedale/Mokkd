<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Integers;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;

class IsIntNotEqualTo implements MatcherContract
{
    private int $expected;

    public function __construct(int $expected)
    {
        $this->expected = $expected;
    }

    public function matches(mixed $actual): bool
    {
        return is_int($actual) && $this->expected != $actual;
    }

    public function describe(Serialiser $serialiser): string
    {
        return "A int not equal to {$this->expected}";
    }
}
