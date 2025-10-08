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

namespace Mokkd\Matchers\Times;

use DateTimeInterface;
use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;

/**
 * The bound is inclusive.
 *
 * Only the time portion of the DateTime is significant - the date part is discarded. So a DateTime representing
 * 2024-04-23 14:00:00 will be considered before 2020-07-14 16:00:00 because 14:00:00 is before 16:00:00.
 *
 * Sub-second time components are discarded.
 */
class IsOnOrBefore implements MatcherContract
{
    private const DisplayFormat = "H:i:s";

    private const ComparisonFormat = "His";

    private int $upperBoundNumeric;

    private DateTimeInterface $upperBound;

    public function __construct(DateTimeInterface $upperBound)
    {
        $this->upperBound = $upperBound;
        $this->upperBoundNumeric = (int) $upperBound->format(self::ComparisonFormat);
    }

    public function matches(mixed $actual): bool
    {
        if (!$actual instanceof DateTimeInterface) {
            return false;
        }

        return ((int) $actual->format(self::ComparisonFormat)) <= $this->upperBoundNumeric;
    }

    public function describe(Serialiser $serialiser): string
    {
        return "A time on or before {$this->upperBound->format(self::DisplayFormat)}";
    }
}
