<?php

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
