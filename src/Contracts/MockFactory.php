<?php

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
