<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Comparisons;

use DateTimeInterface;
use LogicException;
use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser as SerialiserContract;

/**
 * @template T of numeric|DateTimeInterface
 * @template U of T
 *
 * An argument matcher that requires the actual value to be between two expected values.
 */
class IsBetween implements MatcherContract
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
        assert(is_int($lowerBound) || is_float($lowerBound) || ($lowerBound instanceof DateTimeInterface), "Expected int, float or DateTimeInterface, found " . get_debug_type($lowerBound));

        assert(
            get_debug_type($lowerBound) === get_debug_type($upperBound)
            || (
                (is_int($lowerBound) || is_float($lowerBound))
                && (is_int($upperBound) || is_float($upperBound))
            ),
            "Expected both numeric or both DateTimeInterface lower and upper bounds, found " . get_debug_type($lowerBound) . " and " . get_debug_type($upperBound)
        );

        assert(!($lowerBound > $upperBound), new LogicException("Expected upper bound >= lower bound, found {$lowerBound} and {$upperBound}"));
        $this->lowerBound = $lowerBound;
        $this->upperBound = $upperBound;
    }

    /** @param mixed $actual The actual value to match. */
    public function matches(mixed $actual): bool
    {
        if ((is_int($actual) || is_float($actual)) && !is_int($this->lowerBound) && !is_float($this->lowerBound)) {
            return false;
        }

        if ($actual instanceof DateTimeInterface && !($this->lowerBound instanceof DateTimeInterface)) {
            return false;
        }

        return !($this->lowerBound > $actual) && !($actual > $this->upperBound);
    }

    public function describe(SerialiserContract $serialiser): string
    {
        return "value between {$serialiser->serialise($this->lowerBound)} and {$serialiser->serialise($this->upperBound)}";
    }
}
