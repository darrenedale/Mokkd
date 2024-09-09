<?php
declare(strict_types=1);

namespace Mokkd\Contracts;

interface Expectation
{
    /** Check whether a set of arguments match the expectation. */
    public function matches(mixed ...$args): bool;

    /** Match the expectation against some arguments, and provide the matched return value. */
    public function match(mixed ...$args): mixed;

    /** How many times was the expectation matched? */
    public function matched(): int;
}
