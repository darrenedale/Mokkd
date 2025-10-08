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

namespace Mokkd\Matchers\Types;

use Mokkd\Contracts\Serialiser as SerialiserContract;
use Mokkd\Matchers\Composite\MatchesAnyOf;

/**
 * Matcher that requires the test value to be null or any associative array whose keys are all strings.
 *
 * Note that an empty array qualifies as a property map.
 */
class IsPropertyMapOrNull extends MatchesAnyOf
{
    public function __construct()
    {
        parent::__construct(new IsNull(), new IsPropertyMap());
    }

    public function describe(SerialiserContract $serialiser): string
    {
        return "(?array) {property-map}";
    }
}
