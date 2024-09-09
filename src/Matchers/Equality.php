<?php

declare(strict_types=1);

namespace Mokkd\Matchers;

use Mokkd\Contracts\Matcher;

/**
 * @template T
 *
 * An argument matcher that requires the actual value to be equal to an expected value.
 */
class Equality implements Matcher
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
}
