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

class EndsWith implements MatcherContract
{
    private string $suffix;

    /**
     * @param string $suffix What the string must end with.
     */
    public function __construct(string $suffix)
    {
        $this->suffix = $suffix;
    }

    public function suffix(): string
    {
        return $this->suffix;
    }

    public function matches(mixed $actual): bool
    {
        if (!is_string($actual)) {
            return false;
        }

        return str_ends_with($actual, $this->suffix);
    }

    public function describe(Serialiser $serialiser): string
    {
        $suffix = str_replace("\"", "\\\"", $this->suffix);
        return "(string) \"…{$suffix}\"";
    }
}
