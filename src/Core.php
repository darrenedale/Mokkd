<?php

declare(strict_types=1);

namespace Mokkd;

use Mokkd\Contracts\MockFunction as MockFunctionContract;

class Core
{
    /** @var MockFunctionContract[] */
    private static array $mocks = [];

    public static function func(?string $functionName = null): MockFunctionContract
    {
        $functionName = strtolower($functionName);

        if (!array_key_exists($functionName, Core::$mocks)) {
            $mock = new MockFunction($functionName);
            self::$mocks[$functionName] = $mock;
        }

        return self::$mocks[$functionName];
    }

    public static function close(): void
    {
        foreach (self::$mocks as $mock) {
            $mock->remove();
        }

        // TODO check expectations
    }
}
