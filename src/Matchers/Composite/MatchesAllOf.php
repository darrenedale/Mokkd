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

namespace Mokkd\Matchers\Composite;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser as SerialiserContract;
use Mokkd\Utilities\IterableAlgorithms;

/**
 * Composite matcher that requires a value to match all of its component matchers.
 */
class MatchesAllOf implements MatcherContract
{
    /** @var MatcherContract[] */
    private array $matchers;

    /**
     * @param MatcherContract $matcher The first matcher that the value must match.
     * @param MatcherContract ...$matchers The other matchers that the value must match.
     */
    public function __construct(MatcherContract $matcher, MatcherContract ...$matchers)
    {
        $this->matchers = [$matcher, ...$matchers];
    }

    public function matches(mixed $actual): bool
    {
        return IterableAlgorithms::all($this->matchers, static fn (MatcherContract $matcher) => $matcher->matches($actual));
    }

    public function describe(SerialiserContract $serialiser): string
    {
        return "(" . implode(
            ") && (",
            iterator_to_array(IterableAlgorithms::transform($this->matchers, static fn (MatcherContract $matcher) => $matcher->describe($serialiser)))
        ) . ")";
    }
}
