<?php
declare(strict_types=1);

namespace Mokkd\Contracts;

interface Expectation
{
    /** @var int Indicates an expectation expected to match any number of times (including 0) */
    public const UnlimitedTimes = -1;

    /** Check whether a set of arguments match the expectation. */
    public function matches(mixed ...$args): bool;

    /** Match the expectation against some arguments, and provide the matched return value. */
    public function match(mixed ...$args): mixed;

    /** How many times was the expectation matched? */
    public function matched(): int;

    /** How many times is this expectation expected to match. */
    public function expected(): int;

    /** Has the expectation been satisfied? */
    public function isSatisfied(): bool;

    /** The message indicating the expectation isn't satisified. */
    public function notSatisfiedMessage(): string;
}
