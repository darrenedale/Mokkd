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
use Mokkd\Utilities\IterableAlgorithms;

/**
 * An argument matcher that requires the actual value to be identical to one of a set of expected values.
 */
class IsIdenticalToAnyOf implements MatcherContract
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
        return in_array($actual, $this->expected, true);
    }

    public function describe(SerialiserContract $serialiser): string
    {
        return "=== " . implode(
            " || === ",
            iterator_to_array(IterableAlgorithms::transform($this->expected, [$serialiser, "serialise"]))
        );
    }
}
