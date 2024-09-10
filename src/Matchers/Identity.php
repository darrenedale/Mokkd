<?php

declare(strict_types=1);

namespace Mokkd\Matchers;

use Mokkd;
use Mokkd\Contracts\Matcher;

/**
 * @template T
 *
 * An argument matcher that requires the actual value to be identical to an expected value.
 */
class Identity implements Matcher
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
        return $actual === $this->expected;
    }

    public function __toString(): string
    {
        return Mokkd::serialiser()->serialise($this->expected);
    }
}
