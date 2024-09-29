<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Floats;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;

/** The bound is inclusive. */
class IsLessThanOrEqualTo implements MatcherContract
{
    use FormatsFloats;

    private float $upperBound;

    public function __construct(float $upperBound)
    {
        $this->upperBound = $upperBound;
    }

    public function matches(mixed $actual): bool
    {
        return is_float($actual) && $this->upperBound >= $actual;
    }

    public function describe(Serialiser $serialiser): string
    {
        return "(float) <= " . self::formatFloat($this->upperBound);
    }
}
