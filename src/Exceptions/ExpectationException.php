<?php

declare(strict_types=1);

namespace Mokkd\Exceptions;

use Mokkd\Contracts\Expectation as ExpectationContract;
use RuntimeException;
use Throwable;

/** Base class for all exceptions triggered by expectations. */
class ExpectationException extends RuntimeException implements Throwable
{
    protected ExpectationContract $expectation;

    public function __construct(ExpectationContract $expectation, string $message = "", ?Throwable $previous = null)
    {
        parent::__construct($message, previous: $previous);
        $this->expectation = $expectation;
    }

    /** Fetch the expectation that triggered the exception. */
    public function getExpectation(): ExpectationContract
    {
        return $this->expectation;
    }
}
