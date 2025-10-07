<?php

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
