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
 * Contract for a factory that produces MockFunction and MockStaticMethod instances.
 */
interface MockFactory
{
    /** Create a mock for a named free function. */
    public function createMockFunction(string $functionName): MockFunction|ExpectationBuilder;

    /** Create a mock for a static method. */
    public function createMockStaticMethod(string $className, string $functionName): MockStaticMethod|ExpectationBuilder;
}
