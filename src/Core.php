<?php

declare(strict_types=1);

namespace Mokkd;

use Mokkd\Contracts\MockFunction as MockFunctionContract;
use Mokkd\Contracts\Serialiser as SerialiserContract;
use Mokkd\Utilities\Serialiser;

class Core
{
    /** @var MockFunctionContract[] */
    private static array $mocks = [];

    private static ?SerialiserContract $serialiser = null;

    public static function serialiser(): SerialiserContract
    {
        if (!self::$serialiser) {
            self::$serialiser = new Serialiser();
        }

        return self::$serialiser;
    }

    public static function setSerialiser(SerialiserContract $serialiser): void
    {
        self::$serialiser = $serialiser;
    }

    public static function func(?string $functionName = null): MockFunctionContract
    {
        $functionName = strtolower($functionName);

        if (!array_key_exists($functionName, Core::$mocks)) {
            $mock = new MockFunction($functionName);
            self::$mocks[$functionName] = $mock;
        }

        return self::$mocks[$functionName];
    }

    private static function checkExpectations(MockFunctionContract $mock): void
    {
        foreach ($mock->expectations() as $expectation) {
            if (!$expectation->isSatisfied()) {
                throw new ExpectationNotMatchedException("{$mock->name()}{$expectation->notSatisfiedMessage()}");
            }
        }
    }

    public static function close(): void
    {
        foreach (self::$mocks as $mock) {
            $mock->uninstall();
        }

        foreach (self::$mocks as $mock) {
            self::checkExpectations($mock);
        }
    }
}
