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
use Mokkd\Matchers\FormatsFloats;

/**
 * Matcher that requires a numeric value equal to a constraint numeric value.
 *
 * Comparing floating point values for equality is subject to precision errors.
 */
class IsEqualTo implements MatcherContract
{
    use FormatsFloats;

    private int|float $expected;

    public function __construct(int|float $expected)
    {
        $this->expected = $expected;
    }

    public function matches(mixed $actual): bool
    {
        if (!is_int($actual) && !is_float($actual)) {
            return false;
        }

        if (is_int($actual) && is_int($this->expected)) {
            return $this->expected == $actual;
        }

        return (float) $this->expected == (float) $actual;
    }

    public function describe(Serialiser $serialiser): string
    {
        return "(int|float) == " . (is_float($this->expected) ? self::formatFloat($this->expected) : $this->expected);
    }
}
