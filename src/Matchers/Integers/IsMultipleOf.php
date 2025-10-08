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

class IsMultipleOf implements MatcherContract
{
    private int $expected;

    public function __construct(int $expected)
    {
        assert(0 !== $expected, new LogicException("Expecting non-zero factor, found 0"));
        $this->expected = $expected;
    }

    public function matches(mixed $actual): bool
    {
        return is_int($actual) && 0 === $actual % $this->expected;
    }

    public function describe(Serialiser $serialiser): string
    {
        return "(int) {multiple of {$this->expected}}";
    }
}
