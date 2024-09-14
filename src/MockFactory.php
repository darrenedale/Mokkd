<?php

declare(strict_types=1);

namespace Mokkd;

use Mokkd\Contracts\ExpectationBuilder as ExpectationBuilderContract;
use Mokkd\Contracts\MockFunction as MockFunctionContract;
use Mokkd\Contracts\MockStaticMethod;

class MockFactory implements Contracts\MockFactory
{
    /** Create a new function mock. */
    public function createMockFunction(string $functionName): MockFunctionContract|ExpectationBuilderContract
    {
        return new MockFunction($functionName);
    }

    public function createMockStaticMethod(string $className, string $functionName): MockStaticMethod|ExpectationBuilderContract
    {
        throw new \LogicException("Not yet implemented.");
    }
}
