<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Comparisons;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser as SerialiserContract;

/**
 * @template T
 *
 * An argument matcher that requires the actual value to be equal to an expected value.
 */
class IsEqualTo implements MatcherContract
{
    /** @var T The expected value. */
    private mixed $expected;

    /** @param T $expected The expected value. */
    public function __construct(mixed $expected)
    {
        $this->expected = $expected;
    }

    /** @param mixed $actual The actual value to match. */
    public function matches(mixed $actual): bool
    {
        return $actual == $this->expected;
    }

    public function describe(SerialiserContract $serialiser): string
    {
        return "== {$serialiser->serialise($this->expected)}";
    }
}
