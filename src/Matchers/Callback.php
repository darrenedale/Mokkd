<?php

declare(strict_types=1);

namespace Mokkd\Matchers;

use LogicException;
use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser as SerialiserContract;

/**
 * @template T
 * An argument matcher that feeds the actual value to a callback to determine whether it matches.
 */
class Callback implements MatcherContract
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

    public function describe(SerialiserContract $serialiser): string
    {
        return "(callback matcher)";
    }
}
