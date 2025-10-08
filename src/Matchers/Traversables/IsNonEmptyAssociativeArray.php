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

namespace Mokkd\Matchers\Traversables;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser as SerialiserContract;

/** Matcher that requires any associative array with at least one element. */
class IsNonEmptyAssociativeArray implements MatcherContract
{
    public function matches(mixed $actual): bool
    {
        return is_array($actual) && 0 < count($actual) && !array_is_list($actual);
    }

    public function describe(SerialiserContract $serialiser): string
    {
        return "(array) {non-empty-associative}";
    }
}
