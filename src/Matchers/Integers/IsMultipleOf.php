<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Integers;

use LogicException;
use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;

class IsMultipleOf implements MatcherContract
{
    private int $expected;

    public function __construct(int $expected)
    {
        assert(0 !== $expected, new LogicException("Expecting non-zero factor, found 0"));
        $this->expected = $expected;
    }

    public function matches(mixed $actual): bool
    {
        return is_int($actual) && 0 === $actual % $this->expected;
    }

    public function describe(Serialiser $serialiser): string
    {
        return "(int) {multiple of {$this->expected}}";
    }
}
