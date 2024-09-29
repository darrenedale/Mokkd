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

    /** Produce a human-readable description of the matcher. */
    public function describe(Serialiser $serialiser): string;
}
