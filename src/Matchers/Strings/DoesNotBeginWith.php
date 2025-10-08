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

namespace Mokkd\Matchers\Strings;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;

class DoesNotBeginWith implements MatcherContract
{
    private string $prefix;

    /**
     * @param string $prefix What the string must begin with.
     */
    public function __construct(string $prefix)
    {
        $this->prefix = $prefix;
    }

    public function matches(mixed $actual): bool
    {
        if (!is_string($actual)) {
            return false;
        }

        return !str_starts_with($actual, $this->prefix);
    }

    public function describe(Serialiser $serialiser): string
    {
        $prefix = str_replace("\"", "\\\"", $this->prefix);
        return "(string) !\"{$prefix}…\"";
    }
}
