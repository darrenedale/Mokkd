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

namespace Mokkd\Matchers\Floats;

use LogicException;
use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;
use Mokkd\Matchers\FormatsFloats;

/** The bounds are inclusive. */
class IsBetween implements MatcherContract
{
    use FormatsFloats;

    private float $lowerBound;

    private float $upperBound;

    public function __construct(float $lowerBound, float $upperBound)
    {
        assert($lowerBound <= $upperBound, new LogicException("Expected upper bound >= lower bound, found {$lowerBound} and {$upperBound}"));
        $this->lowerBound = $lowerBound;
        $this->upperBound = $upperBound;
    }

    public function matches(mixed $actual): bool
    {
        return is_float($actual) && $this->lowerBound <= $actual && $actual <= $this->upperBound;
    }

    public function describe(Serialiser $serialiser): string
    {
        return "(float) >= " . self::formatFloat($this->lowerBound) . " && <= " . self::formatFloat($this->upperBound);
    }
}
