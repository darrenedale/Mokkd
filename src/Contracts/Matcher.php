<?php

declare(strict_types=1);

namespace Mokkd\Contracts;

/**
 * Contract for argument matchers for expectations.
 */
interface Matcher
{
    /** Determine whether a value satisfies the constraints of the matcher. */
    public function matches(mixed $actual): bool;

    /** Get a representation of the matcher suitable for informing the user of what would be expected to match. */
    public function __toString(): string;
}
