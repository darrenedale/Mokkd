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

namespace Mokkd\Expectations;

/** Enumerates the ways in which an expectation might determine the return value for a mocked function call. */
enum ReturnMode
{
    /** There is no return value. */
    case Void;

    /** Return a static value. */
    case Value;

    /** Return the value returned from a callback. */
    case Callback;

    /** Return sequential values from an array. */
    case Sequential;

    /** Return a value mapped from an associative array. */
    case Mapped;

    /** Throw an exception rather than return a value. */
    case Throw;
}
