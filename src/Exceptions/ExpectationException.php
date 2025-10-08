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

namespace Mokkd\Exceptions;

use Mokkd\Contracts\Expectation as ExpectationContract;
use RuntimeException;
use Throwable;

/** Base class for all exceptions triggered by expectations. */
class ExpectationException extends RuntimeException implements Throwable
{
    protected ExpectationContract $expectation;

    public function __construct(ExpectationContract $expectation, string $message = "", ?Throwable $previous = null)
    {
        parent::__construct($message, previous: $previous);
        $this->expectation = $expectation;
    }

    /** Fetch the expectation that triggered the exception. */
    public function getExpectation(): ExpectationContract
    {
        return $this->expectation;
    }
}
