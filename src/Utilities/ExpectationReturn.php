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

namespace Mokkd\Utilities;

use LogicException;

class ExpectationReturn implements \Mokkd\Contracts\ExpectationReturn
{
    private bool $isVoid;

    private mixed $value;

    private function __construct(mixed $value, bool $isVoid)
    {
        $this->isVoid = $isVoid;
        $this->value = $value;
    }

    public static function void(): self
    {
        return new self(null, true);
    }

    public static function create(mixed $value): self
    {
        return new self($value, false);
    }

    public function isVoid(): bool
    {
        return $this->isVoid;
    }

    public function value(): mixed
    {
        assert(!$this->isVoid, new LogicException("value() called on void return"));
        return $this->value;
    }
}
