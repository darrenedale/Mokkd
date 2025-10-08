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

namespace MokkdTests;

use Mokkd\Utilities\Guard;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use ReflectionException;
use ReflectionMethod;

class TestCase extends PHPUnitTestCase
{
    protected const ZendAssertionsActive = 1;

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

    /** Skip the current test if assertions are not being executed. */
    protected static function skipIfAssertionsDisabled(): void
    {
        if (self::ZendAssertionsActive !== (int) ini_get("zend.assertions")) {
            self::markTestSkipped("This test requires assertions to be enabled.");
        }
    }

    protected function markTestPassedWithoutAssertions(): void
    {
        self::assertTrue(true);
    }
}
