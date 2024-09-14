<?php

declare(strict_types=1);

use Mokkd\Contracts\ExpectationBuilder;
use Mokkd\Contracts\MockFactory as MockFactoryContract;
use Mokkd\Contracts\MockFunction as MockFunctionContract;
use Mokkd\Contracts\Serialiser as SerialiserContract;
use Mokkd\MockFactory;
use Mokkd\Utilities\Guard;
use Mokkd\Utilities\Serialiser;

/** Static factory class for generating function mocks. */
class Mokkd
{
    /** @var MockFunctionContract[] */
    private static array $mocks = [];

    private static ?MockFactoryContract $mockFactory = null;

    private static ?SerialiserContract $serialiser = null;

    /**
     * Fetch a mock for a function.
     *
     * TODO Functions in namespaces don't currently work - need to check UOPZ for why.
     *
     * @param string $functionName The name of the function to mock.
     */
    public static function func(string $functionName): MockFunctionContract|ExpectationBuilder
    {
        $key = strtolower($functionName);

        if (!array_key_exists($key, Mokkd::$mocks)) {
            self::$mocks[$key] = self::factory()->createMockFunction($functionName);
        }

        return self::$mocks[$key];
    }

    /** Fetch the installed mock factory. */
    public static function factory(): MockFactoryContract
    {
        if (!self::$mockFactory) {
            self::$mockFactory = new MockFactory();
        }

        return self::$mockFactory;
    }

    /** Set the factory to use when new mocks need to be created. */
    public static function setFactory(MockFactoryContract $factory): void
    {
        self::$mockFactory = $factory;
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
        // ensure everything is cleaned up no matter how we exit this method
        $guard = new Guard(static function () {
            self::$mocks = [];
            self::$mockFactory = null;
            self::$serialiser = null;
        });

        foreach (self::$mocks as $mock) {
            $mock->uninstall();
        }

        foreach (self::$mocks as $mock) {
            $mock->verifyExpectations();
        }
    }
}
