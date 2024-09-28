<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Strings;

use Closure;
use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;
use Mokkd\Utilities\Guard;

class MatchingPattern implements MatcherContract
{
    private string $pattern;

    private string $encoding;

    private Closure $compare;

    /**
     * @param string $pattern The regular expression, without the // delimiters.
     * @param string $encoding The character encoding (defaults to UTF-8).
     */
    public function __construct(string $pattern, string $encoding = "UTF-8", bool $caseSensitive = true)
    {
        // TODO assert valid pattern and encoding
        $this->pattern = $pattern;
        $this->encoding = $encoding;

        if ($caseSensitive) {
            $this->compare = static fn(string $pattern, string $subject): bool => mb_ereg($pattern, $subject);
        } else {
            $this->compare = static fn(string $pattern, string $subject): bool => mb_eregi($pattern, $subject);
        }
    }

    public function matches(mixed $actual): bool
    {
        if (!is_string($actual)) {
            return false;
        }

        $previousEncoding = mb_regex_encoding();
        $encodingsDiffer = ($previousEncoding !== $this->encoding);

        if ($encodingsDiffer) {
            // ensure the encoding is reset no matter how we exit this method
            $guard = new Guard(static fn() => mb_regex_encoding($previousEncoding));
            mb_regex_encoding($this->encoding);
        }

        return ($this->compare)($this->pattern, $actual);
    }

    public function describe(Serialiser $serialiser): string
    {
        return "A {$this->encoding} string matching the regular expression {$this->pattern}";
    }
}
