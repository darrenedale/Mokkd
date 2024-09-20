<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Strings;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;

class IsStringNotBeginningWith implements MatcherContract
{
    private string $prefix;

    /**
     * @param string $prefix What the string must begin with.
     */
    public function __construct(string $prefix)
    {
        $this->prefix = $prefix;
    }

    public function matches(mixed $actual): bool
    {
        if (!is_string($actual)) {
            return false;
        }

        return !str_starts_with($actual, $this->prefix);
    }

    public function describe(Serialiser $serialiser): string
    {
        $prefix = str_replace("\"", "\\\"", $this->prefix);
        return "(string) !\"{$prefix}…\"";
    }
}
