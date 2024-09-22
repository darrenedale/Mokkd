<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Strings;

use LogicException;
use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;

class IsStringNoLongerThan implements MatcherContract
{
    private int $length;

    private string $encoding;

    public function __construct(int $length, string $encoding = "UTF-8")
    {
        assert(0 <= $length, new LogicException("Expecting length >= 0, found {$length}"));
        assert(in_array($encoding, mb_list_encodings(), true), new LogicException("Expected supported character encoding, found \"{$encoding}\""));
        $this->length = $length;
        $this->encoding = $encoding;
    }

    public function length(): int
    {
        return $this->length;
    }

    public function encoding(): string
    {
        return $this->encoding;
    }

    public function matches(mixed $actual): bool
    {
        return is_string($actual) && $this->length >= mb_strlen($actual, $this->encoding);
    }

    public function describe(Serialiser $serialiser): string
    {
        return "({$this->encoding}-string[<={$this->length}])";
    }
}
