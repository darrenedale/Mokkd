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

use LogicException;
use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;

class IsNoShorterThan implements MatcherContract
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
        return is_string($actual) && $this->length <= mb_strlen($actual, $this->encoding);
    }

    public function describe(Serialiser $serialiser): string
    {
        return "({$this->encoding}-string[>={$this->length}])";
    }
}
