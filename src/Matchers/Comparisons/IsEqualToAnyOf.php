<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Comparisons;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser as SerialiserContract;
use Mokkd\Utilities\IterableAlgorithms;

/**
 * An argument matcher that requires the actual value to be equal to one of a set of expected values.
 */
class IsEqualToAnyOf implements MatcherContract
{
    /** @var array The set of possible matching values. */
    private array $expected;

    /**
     * @param mixed $expected The first value to match against.
     * @param mixed $otherExpected The other values to match against.
     */
    public function __construct(mixed $expected, mixed ...$otherExpected)
    {
        $this->expected = [$expected, ...$otherExpected];
    }

    /** @param mixed $actual The actual value to match. */
    public function matches(mixed $actual): bool
    {
        return in_array($actual, $this->expected, false);
    }

    public function describe(SerialiserContract $serialiser): string
    {
        return "== " . implode(
            " || == ",
            iterator_to_array(IterableAlgorithms::transform($this->expected, [$serialiser, "serialise"]))
        );
    }
}
