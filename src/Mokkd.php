<?php

declare(strict_types=1);

use Mokkd\Contracts\MockFunction as MockFunctionContract;
use Mokkd\Contracts\Serialiser as SerialiserContract;
use Mokkd\MockFunction;
use Mokkd\Utilities\Guard;
use Mokkd\Utilities\Serialiser;

/** Static factory class for generating function mocks. */
class Mokkd
{
    /** @var MockFunctionContract[] */
    private static array $mocks = [];

    private static ?SerialiserContract $serialiser = null;

    /**
     * Fetch a mock for a function.
     *
     * TODO Functions in namespaces don't currently work - need to check UOPZ for why.
     *
     * @param string $functionName The name of the function to mock.
     * @return MockFunction
     */
    public static function func(string $functionName): MockFunction
    {
        $key = strtolower($functionName);

        if (!array_key_exists($key, Mokkd::$mocks)) {
            $mock = new MockFunction($functionName);
            self::$mocks[$key] = $mock;
        }

        return self::$mocks[$key];
    }


    /** Fetch the installed serialiser. */
    public static function serialiser(): SerialiserContract
    {
        if (!self::$serialiser) {
            self::$serialiser = new Serialiser();
        }

        return self::$serialiser;
    }

    /** Install a new serialiser. */
    public static function setSerialiser(SerialiserContract $serialiser): void
    {
        self::$serialiser = $serialiser;
    }

    /** Close the mock session */
    public static function close(): void
    {
        // ensure all mocks are unregistered no matter how we exit this method
        $guard = new Guard(static fn() => self::$mocks = []);

        foreach (self::$mocks as $mock) {
            $mock->uninstall();
        }

        foreach (self::$mocks as $mock) {
            $mock->verifyExpectations();
        }
    }
}
