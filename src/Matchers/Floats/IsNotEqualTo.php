<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Floats;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;
use Mokkd\Matchers\FormatsFloats;

/**
 * Matcher that requires a float not equal to 0.0.
 */
class IsNotEqualTo implements MatcherContract
{
    use FormatsFloats;

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
        return "(float) != " . self::formatFloat($this->expected);
    }
}
