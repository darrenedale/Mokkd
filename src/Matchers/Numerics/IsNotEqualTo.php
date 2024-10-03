<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Numerics;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;
use Mokkd\Matchers\FormatsFloats;

/** Matcher that requires a numeric value not equal to a constraint numeric value. */
class IsNotEqualTo implements MatcherContract
{
    use FormatsFloats;

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
            return $this->expected != $actual;
        }

        return (float) $this->expected != (float) $actual;
    }

    public function describe(Serialiser $serialiser): string
    {
        return "(int|float) != " . (is_float($this->expected) ? self::formatFloat($this->expected) : $this->expected);
    }
}
