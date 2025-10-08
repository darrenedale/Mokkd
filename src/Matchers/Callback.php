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

namespace Mokkd\Matchers;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser as SerialiserContract;

/**
 * @template T
 * An argument matcher that feeds the test value to a callback to determine whether it matches.
 */
class Callback implements MatcherContract
{
    /** @var callable(T): bool */
    private $fn;

    /** @param callable(T): bool $fn */
    public function __construct(callable $fn)
    {
        $this->fn = $fn;
    }

    /** @param T $actual */
    public function matches(mixed $actual): bool
    {
        return ($this->fn)($actual);
    }

    public function describe(SerialiserContract $serialiser): string
    {
        return "(callback matcher)";
    }
}
