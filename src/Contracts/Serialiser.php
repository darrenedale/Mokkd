<?php

declare(strict_types=1);

namespace Mokkd\Contracts;

interface Serialiser
{
    /** @return string[] */
    public function serialise(mixed ...$args): array;
}
