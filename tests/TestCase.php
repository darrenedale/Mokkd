<?php

declare(strict_types=1);

namespace MokkdTests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use ReflectionException;
use ReflectionMethod;

class TestCase extends PHPUnitTestCase
{
    /**
     * @param class-string|object $object The object or class with a protected or private method.
     * @param string $methodName The protected or private method name.
     * @return callable A closure that can be invoked to call the protected or private method.
     * @throws ReflectionException
     */
    protected static function accessibleMethod(string|object $object, string $methodName): callable
    {
        return (new ReflectionMethod($object, $methodName))->getClosure($object);
    }
}
