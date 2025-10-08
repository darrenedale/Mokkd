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

namespace Mokkd;

use LogicException;
use Mokkd\Contracts\ExpectationBuilder as ExpectationBuilderContract;
use Mokkd\Contracts\MockFunction as MockFunctionContract;
use Mokkd\Contracts\MockStaticMethod as MockStaticMethodContract;
use Mokkd\Contracts\Serialiser as SerialiserContract;

class MockFactory implements Contracts\MockFactory
{
    private SerialiserContract $serialiser;

    public function __construct(SerialiserContract $serialiser)
    {
        $this->serialiser = $serialiser;
    }

    /** Create a new function mock. */
    public function createMockFunction(string $functionName): MockFunctionContract|ExpectationBuilderContract
    {
        return new MockFunction($functionName, $this->serialiser);
    }

    public function createMockStaticMethod(string $className, string $functionName): MockStaticMethodContract|ExpectationBuilderContract
    {
        throw new LogicException("Not yet implemented.");
    }
}
