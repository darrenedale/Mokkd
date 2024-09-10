<?php

declare(strict_types=1);

namespace Mokkd\Matchers;

use Mokkd\Contracts\Matcher;

/**
 * @template T
 * An argument matcher that feeds the actual value to a callback to determine whether it matches.
 */
class Callback implements Matcher
{
    /** @var callable(T): bool */
    private $fn;

    /** @param callable(T): bool $fn */
    public function __construct(callable $fn)
    {
        $this->fn = $fn;
    }

    /** @param T $actual */
    public function matches(mixed $actual): bool
    {
        return ($this->fn)($actual);
    }

    public function __toString(): string
    {
        return "(callback matcher)";
    }
}
