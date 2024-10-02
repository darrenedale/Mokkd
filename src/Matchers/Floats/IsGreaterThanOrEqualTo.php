<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Floats;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;
use Mokkd\Matchers\FormatsFloats;

/** The bound is inclusive. */
class IsGreaterThanOrEqualTo implements MatcherContract
{
    use FormatsFloats;

    private float $lowerBound;

    public function __construct(float $lowerBound)
    {
        $this->lowerBound = $lowerBound;
    }

    public function matches(mixed $actual): bool
    {
        return is_float($actual) && $this->lowerBound <= $actual;
    }

    public function describe(Serialiser $serialiser): string
    {
        return "(float) >= " . self::formatFloat($this->lowerBound);
    }
}
