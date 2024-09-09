<?php

declare(strict_types=1);

namespace Mokkd\Contracts;

interface Matcher
{
    public function matches(mixed $actual): bool;
}
