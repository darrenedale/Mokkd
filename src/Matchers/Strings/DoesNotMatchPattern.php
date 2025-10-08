<?php

/*
 * Copyright 2025 Darren Edale
 *
 * This file is part of the Mokkd package.
 *
 * Mokkd is free software: you can redistribute it and/or modify
 * it under the terms of the Apache License v2.0.
 *
 * Mokkd is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * Apache License for more details.
 *
 * You should have received a copy of the Apache License v2.0
 * along with Mokkd. If not, see <http://www.apache.org/licenses/>.
 */

declare(strict_types=1);

namespace Mokkd\Matchers\Strings;

use Closure;
use LogicException;
use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;
use Mokkd\Utilities\Guard;

class DoesNotMatchPattern implements MatcherContract
{
    use ValidatesRegularExpressions;

    private string $pattern;

    private string $encoding;

    private Closure $compare;

    /**
     * @param string $pattern The regular expression, without the // delimiters.
     * @param string $encoding The character encoding (defaults to UTF-8).
     */
    public function __construct(string $pattern, string $encoding = "UTF-8", bool $caseSensitive = true)
    {
        assert(in_array($encoding, mb_list_encodings(), true), new LogicException("Expected character encoding supported by the mbstring extension, found {$encoding}"));
        assert(self::isValidRegularExpression($pattern), new LogicException("Expected valid ereg regular expression, found {$pattern}"));

        $this->pattern = $pattern;
        $this->encoding = $encoding;

        if ($caseSensitive) {
            $this->compare = static fn(string $pattern, string $subject): bool => !mb_ereg($pattern, $subject);
        } else {
            $this->compare = static fn(string $pattern, string $subject): bool => !mb_eregi($pattern, $subject);
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

        return !($this->compare)($this->pattern, $actual);
    }

    public function describe(Serialiser $serialiser): string
    {
        return "({$this->encoding}-string) !~= {$this->pattern}";
    }
}
