<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Strings;

use JsonException;
use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;

class IsJsonString implements MatcherContract
{
    public function matches(mixed $actual): bool
    {
        if (!is_string($actual)) {
            return false;
        }

        try {
            json_decode($actual, flags: JSON_THROW_ON_ERROR);
            return true;
        } catch (JsonException) {
            return false;
        }
    }

    public function describe(Serialiser $serialiser): string
    {
        return "A JSON string";
    }
}
