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

namespace Mokkd\Matchers\Dates;

use DateTimeInterface;
use LogicException;
use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;

/**
 * Matcher that requires a date in a given year.
 */
class IsInYear implements MatcherContract
{
    private int $year;

    public function __construct(int $year)
    {
        $this->year = $year;
    }

    public function matches(mixed $actual): bool
    {
        return ($actual instanceof DateTimeInterface) && ((int) $actual->format("Y")) === $this->year;
    }

    public function describe(Serialiser $serialiser): string
    {
        return sprintf("(DateTimeInterface) {year == %04d}", $this->year);
    }
}
