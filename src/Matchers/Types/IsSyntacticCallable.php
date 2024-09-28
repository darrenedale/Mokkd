<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Types;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser as SerialiserContract;

/**
 * Matcher that requires the test value to be any callable.
 *
 * The callable is required to be only syntactically callable, it need not actually exist.
 */
class IsSyntacticCallable implements MatcherContract
{
    public function matches(mixed $actual): bool
    {
        return is_callable($actual, true);
    }

    public function describe(SerialiserContract $serialiser): string
    {
        return "(callable) {any}";
    }
}
