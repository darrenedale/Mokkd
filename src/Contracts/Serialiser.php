<?php

declare(strict_types=1);

namespace Mokkd\Contracts;

/** Contract for serialisers of mock function call arguments and expectation constraints. */
interface Serialiser
{
    /**
     * @param mixed $value The value to serialiser.
     * @return string The serialisation of the value.
     */
    public function serialise(mixed $value): string;
}
