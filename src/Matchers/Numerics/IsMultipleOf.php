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

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;

class IsMultipleOf implements MatcherContract
{
    private int|float $expected;

    public function __construct(int|float $expected)
    {
        $this->expected = $expected;
    }

    public function matches(mixed $actual): bool
    {
        if (is_int($actual) && is_int($this->expected)) {
            return 0 === $actual % $this->expected;
        }

        $actual = (float) $actual;
        $expected = (float) $this->expected;

        return 0.0 === ($actual / $expected) - (floor($actual / $expected));
    }

    public function describe(Serialiser $serialiser): string
    {
        return "A float that's a multiple of {$this->expected}";
    }
}
