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

namespace Mokkd\Contracts;

/**
 * Contract for test expectations set on mock functions.
 */
interface Expectation
{
    /** @var int Indicates an expectation expected to match any number of times (including 0) */
    public const UnlimitedTimes = -1;

    /** Check whether a set of arguments match the expectation. */
    public function matches(mixed ...$args): bool;

    /** Match the expectation against some arguments, and provide the matched return value (if there is one). */
    public function match(mixed ...$args): ExpectationReturn;

    /** Has the expectation been satisfied? */
    public function isSatisfied(): bool;

    /** The message indicating the expectation isn't satisified. */
    public function message(Serialiser $serialiser): string;
}
