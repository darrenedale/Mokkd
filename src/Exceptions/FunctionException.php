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

use RuntimeException;
use Throwable;

class FunctionException extends RuntimeException implements Throwable
{
    private string $functionName;

    public function __construct(string $functionName, string $message = "", ?Throwable $previous = null)
    {
        parent::__construct($message, previous: $previous);
        $this->functionName = $functionName;
    }

    /** Fetch the name of the functin that triggered the exception. */
    public function getFunctionName(): string
    {
        return $this->functionName;
    }
}
