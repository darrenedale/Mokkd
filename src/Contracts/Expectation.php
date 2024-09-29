<?php
declare(strict_types=1);

namespace Mokkd\Contracts;

/**
 * Contract for test expectations set on mock functions.
 */
interface Expectation
{
    /** @var int Indicates an expectation expected to match any number of times (including 0) */
    public const UnlimitedTimes = -1;

    /** Check whether a set of arguments match the expectation. */
    public function matches(mixed ...$args): bool;

    /** Match the expectation against some arguments, and provide the matched return value. */
    public function match(mixed ...$args): mixed;

    /** Has the expectation been satisfied? */
    public function isSatisfied(): bool;

    /** The message indicating the expectation isn't satisified. */
    public function message(Serialiser $serialiser): string;
}
