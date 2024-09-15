<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Strings;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;

class IsStringEndingWith implements MatcherContract
{
    private string $suffix;

    /**
     * @param string $suffix What the string must end with.
     */
    public function __construct(string $suffix)
    {
        $this->suffix = $suffix;
    }

    public function matches(mixed $actual): bool
    {
        if (!is_string($actual)) {
            return false;
        }

        return str_ends_with($actual, $this->suffix);
    }

    public function describe(Serialiser $serialiser): string
    {
        $suffix = str_replace("\"", "\\\"", $this->suffix);
        return "A string ending with \"{$suffix}\"";
    }
}
