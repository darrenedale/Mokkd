<?php

/*
 * Copyright 2025 Darren Edale
 *
 * This file is part of the Mokkd package.
 *
 * Mokkd is free software: you can redistribute it and/or modify
 * it under the terms of the Apache License v2.0.
 *
 * Mokkd is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * Apache License for more details.
 *
 * You should have received a copy of the Apache License v2.0
 * along with Mokkd. If not, see <http://www.apache.org/licenses/>.
 */

declare(strict_types=1);

namespace Mokkd\Matchers\Comparisons;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser as SerialiserContract;

/**
 * @template T
 *
 * An argument matcher that requires the actual value to be identical to an expected value.
 */
class IsIdenticalTo implements MatcherContract
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

    public function describe(SerialiserContract $serialiser): string
    {
        return "=== {$serialiser->serialise($this->expected)}";
    }
}
