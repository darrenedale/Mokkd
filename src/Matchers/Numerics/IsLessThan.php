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
 * The bound is exclusive.
 */
class IsLessThan implements MatcherContract
{
    private int|float $upperBound;

    public function __construct(int|float $upperBound)
    {
        $this->upperBound = $upperBound;
    }

    public function matches(mixed $actual): bool
    {
        if (!is_int($actual) && !is_float($actual)) {
            return false;
        }

        if (is_int($actual) && is_int($this->upperBound)) {
            return $this->upperBound > $actual;
        }

        return (float) $this->upperBound > (float) $actual;
    }

    public function describe(Serialiser $serialiser): string
    {
        return "A numeric value less than {$serialiser->serialise($this->upperBound)}";
    }
}
