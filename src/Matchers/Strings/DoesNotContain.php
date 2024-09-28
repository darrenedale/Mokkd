<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Strings;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;

class DoesNotContain implements MatcherContract
{
    private string $infix;

    /**
     * @param string $infix What the string must end with.
     */
    public function __construct(string $infix)
    {
        $this->infix = $infix;
    }

    public function matches(mixed $actual): bool
    {
        if (!is_string($actual)) {
            return false;
        }

        return !str_contains($actual, $this->infix);
    }

    public function describe(Serialiser $serialiser): string
    {
        $infix = str_replace("\"", "\\\"", $this->infix);
        return "A string not containing \"{$infix}\"";
    }
}
