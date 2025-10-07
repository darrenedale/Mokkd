<?php

declare(strict_types=1);

namespace Mokkd\Contracts;

use LogicException;

interface ExpectationReturn
{
    /** Flag indicating whether the expectation's return value is void. */
    public function isVoid(): bool;

    /**
     * The expectation's returned value.
     *
     * @throws LogicException if called on a void return.
     */
    public function value(): mixed;
}
