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
use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;

/**
 * Matcher that requires a date on or before a bounding date
 *
 * Only the date portion of the DateTime is significant - the time part is discarded.
 */
class IsOnOrBefore implements MatcherContract
{
    private const DisplayFormat = "Y-m-d";

    private const ComparisonFormat = "Ymd";

    private int $upperBoundNumeric;

    private DateTimeInterface $upperBound;

    /**
     * @param DateTimeInterface $upperBound The date the value must be on or before.
     */
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
        return "(DateTimeInterface) <= {$this->upperBound->format(self::DisplayFormat)}";
    }
}
