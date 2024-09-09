<?php

declare(strict_types=1);

namespace Mokkd\Utilities;

/** Scope guard. */
final class Guard
{
    private $fn;

    public function __construct(callable $fn)
    {
        $this->fn = $fn;
    }

    public function __destruct()
    {
        ($this->fn)();
    }
}
