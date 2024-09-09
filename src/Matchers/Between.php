<?php

declare(strict_types=1);

namespace Mokkd\Matchers;

use DateTimeInterface;
use Mokkd\Contracts\Matcher;

/**
 * @template T of int|float|DateTimeInterface
 *
 * An argument matcher that requires the actual value to be between two expected values.
 */
class Between implements Matcher
{
    /** @var T The lowest expected value. */
    private mixed $lowerBound;

    /** @var T The highest expected value. */
    private mixed $upperBound;

    /**
     * @param T $lowerBound The lowest expected value.
     * @param T $upperBound The highest expected value.
     */
    public function __construct(mixed $lowerBound, mixed $upperBound)
    {
        assert(!($lowerBound > $upperBound));
        $this->lowerBound = $lowerBound;
        $this->upperBound = $upperBound;
    }

    /** @param mixed $actual The actual value to match. */
    public function matches(mixed $actual): bool
    {
        return !($this->lowerBound > $actual) && !($actual > $this->upperBound);
    }
}
