<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Types;

use Mokkd\Contracts\Serialiser as SerialiserContract;
use Mokkd\Matchers\Composite\MatchesAnyOf;

/**
 * Matcher that requires the test value to be any callable or null.
 *
 * The callable is required to be only syntactically callable, it need not actually exist.
 */
class IsSyntacticCallableOrNull extends MatchesAnyOf
{
    public function __construct()
    {
        parent::__construct(new IsNull(), New IsSyntacticCallable());
    }

    public function describe(SerialiserContract $serialiser): string
    {
        return "(?callable) {any}";
    }
}
