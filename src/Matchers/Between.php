<?php

declare(strict_types=1);

namespace Mokkd\Matchers;

use DateTimeInterface;
use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser as SerialiserContract;

/**
 * @template T of numeric|DateTimeInterface
 * @template U of T
 *
 * An argument matcher that requires the actual value to be between two expected values.
 */
class Between implements MatcherContract
{
    /** @var T The lowest expected value. */
    private mixed $lowerBound;

    /** @var U The highest expected value. */
    private mixed $upperBound;

    /**
     * @param T $lowerBound The lowest expected value.
     * @param U $upperBound The highest expected value.
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

    public function describe(SerialiserContract $serialiser): string
    {
        return "value between {$serialiser->serialise($this->lowerBound)} and {$serialiser->serialise($this->upperBound)}";
    }
}
