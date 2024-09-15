<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Strings;

use LogicException;
use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;

class IsStringOfMoreBytesThan implements MatcherContract
{
    private int $length;

    public function __construct(int $length)
    {
        // TODO assert encoding
        assert(0 <= $length, new LogicException("Expecting length >= 0, found {$length}"));
        $this->length = $length;
    }

    public function matches(mixed $actual): bool
    {
        return is_string($actual) && $this->length < strlen($actual);
    }

    public function describe(Serialiser $serialiser): string
    {
        return "A string of more than {$this->length} bytes";
    }
}
