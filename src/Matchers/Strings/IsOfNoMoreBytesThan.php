<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Strings;

use LogicException;
use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;

class IsOfNoMoreBytesThan implements MatcherContract
{
    private int $length;

    public function __construct(int $length)
    {
        assert(0 <= $length, new LogicException("Expecting length >= 0, found {$length}"));
        $this->length = $length;
    }

    public function length(): int
    {
        return $this->length;
    }

    public function matches(mixed $actual): bool
    {
        return is_string($actual) && $this->length >= strlen($actual);
    }

    public function describe(Serialiser $serialiser): string
    {
        return "(string[<={$this->length}])";
    }
}
