<?php

declare(strict_types=1);

namespace Mokkd\Expectations;

/** Enumerates the ways in which an expectation might determine the return value for a mocked function call. */
enum ReturnMode
{
    /** Return a static value. */
    case Value;

    /** Return the value returned from a callback. */
    case Callback;

    /** Return sequential values from an array. */
    case Sequential;

    /** Return a value mapped from an associative array. */
    case Mapped;
}
