<?php
declare(strict_types=1);

namespace Mokkd\Exceptions;

use RuntimeException;
use Throwable;

class FunctionException extends RuntimeException implements Throwable
{
    private string $functionName;

    public function __construct(string $functionName, string $message = "", ?Throwable $previous = null)
    {
        parent::__construct($message, previous: $previous);
        $this->functionName = $functionName;
    }

    /** Fetch the name of the functin that triggered the exception. */
    public function getFunctionName(): string
    {
        return $this->functionName;
    }
}
