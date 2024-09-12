<?php

declare(strict_types=1);

namespace MokkdTests\Utilities;

use Mokkd\Utilities\Guard;
use MokkdTests\TestCase;
use MokkdTests\TestException;
use PHPUnit\Framework\Attributes\DataProvider;

class GuardTest extends TestCase
{
    public const TestExceptionMessage = "The guard callback was called";

    public static function dataForTestGuard1(): iterable
    {
        yield "closure" => [fn() => throw new TestException(self::TestExceptionMessage)];

        yield "invokable" => [
            new class()
            {
                public function __invoke(): void
                {
                    throw new TestException(GuardTest::TestExceptionMessage);
                }
            }
        ];

        $object = new class
        {
            public function guard(): void
            {
                throw new TestException(GuardTest::TestExceptionMessage);
            }

            public static function staticGuard(): void
            {
                throw new TestException(GuardTest::TestExceptionMessage);
            }
        };

        $objectClass = $object::class;

        yield "static-method-tuple" => [[$object::class, "staticGuard"]];
        yield "instance-method-tuple" => [[$object, "guard"]];
        yield "static-method-string" => ["{$objectClass}::staticGuard"];

        function guard()
        {
            throw new TestException(GuardTest::TestExceptionMessage);
        }

        yield "function-name" => ["\MokkdTests\Utilities\guard"];
    }

    /** Ensure all types of callable are supported. */
    #[DataProvider("dataForTestGuard1")]
    public function testGuard1(mixed $fn): void
    {
        $this->expectException(TestException::class);
        $this->expectExceptionMessage(self::TestExceptionMessage);
        $guard = new Guard($fn);
        unset($guard);
    }
}
