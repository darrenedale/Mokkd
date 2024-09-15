<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Strings;

use LogicException;
use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;

class IsStringLongerThan implements MatcherContract
{
    private int $length;

    private string $encoding;

    public function __construct(int $length, string $encoding = "UTF-8")
    {
        // TODO assert encoding
        assert(0 <= $length, new LogicException("Expecting length >= 0, found {$length}"));
        $this->length = $length;
        $this->encoding = $encoding;
    }

    public function matches(mixed $actual): bool
    {
        return is_string($actual) && $this->length < mb_strlen($actual, $this->encoding);
    }

    public function describe(Serialiser $serialiser): string
    {
        return "A {$this->encoding} string of more than {$this->length} characters";
    }
}
