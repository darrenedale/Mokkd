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

namespace Mokkd\Matchers\Numerics;

use LogicException;
use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;

/**
 * The bounds are inclusive.
 */
class IsBetween implements MatcherContract
{
    private int|float $lowerBound;

    private int|float $upperBound;

    private bool $canUseIntegralComparison;

    public function __construct(int|float $lowerBound, int|float $upperBound)
    {
        assert($lowerBound <= $upperBound, new LogicException("Expected upper bound >= lower bound, found {$lowerBound} and {$upperBound}"));
        $this->lowerBound = $lowerBound;
        $this->upperBound = $upperBound;
        $this->canUseIntegralComparison = is_int($lowerBound) && is_int($upperBound);
    }

    public function matches(mixed $actual): bool
    {
        if (!is_int($actual) && !is_float($actual)) {
            return false;
        }

        if (is_int($actual) && $this->canUseIntegralComparison) {
            return $this->lowerBound <= $actual && $actual <= $this->upperBound;
        }

        $actual = (float) $actual;
        return (float) $this->lowerBound <= $actual && $actual <= (float) $this->upperBound;
    }

    public function describe(Serialiser $serialiser): string
    {
        return "A numeric value between {$serialiser->serialise($this->lowerBound)} and {$serialiser->serialise($this->upperBound)} inclusive";
    }
}
