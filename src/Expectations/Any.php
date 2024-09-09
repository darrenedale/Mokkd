<?php
declare(strict_types=1);

namespace Mokkd\Expectations;

class Any extends AbstractExpectation
{
    public function matches(...$args): bool
    {
        return true;
    }
}
