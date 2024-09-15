<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Strings;

use LogicException;
use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;

class IsEmptyString implements MatcherContract
{
    public function matches(mixed $actual): bool
    {
        return is_string($actual) && 0 === strlen($actual);
    }

    public function describe(Serialiser $serialiser): string
    {
        return "An empty string";
    }
}
