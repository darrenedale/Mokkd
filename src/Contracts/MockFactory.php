<?php

declare(strict_types=1);

namespace Mokkd\Contracts;

/** Contract for a factory that produces MockFunction and MockStaticMethod instances. */
interface MockFactory
{
    public function createMockFunction(string $functionName): MockFunction|ExpectationBuilder;

    public function createMockStaticMethod(string $className, string $functionName): MockStaticMethod|ExpectationBuilder;
}
