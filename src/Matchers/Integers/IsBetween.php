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

namespace Mokkd\Matchers\Integers;

use LogicException;
use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;

/** The bounds are inclusive. */
class IsBetween implements MatcherContract
{
    private int $lowerBound;

    private int $upperBound;

    public function __construct(int $lowerBound, int $upperBound)
    {
        assert($lowerBound <= $upperBound, new LogicException("Expected upper bound >= lower bound, found {$lowerBound} and {$upperBound}"));
        $this->lowerBound = $lowerBound;
        $this->upperBound = $upperBound;
    }

    public function matches(mixed $actual): bool
    {
        return is_int($actual) && $this->lowerBound <= $actual && $actual <= $this->upperBound;
    }

    public function describe(Serialiser $serialiser): string
    {
        return "(int) >= {$this->lowerBound} && <= {$this->upperBound}";
    }
}
