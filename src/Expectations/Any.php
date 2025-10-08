<?php

/*
 * Copyright 2025 Darren Edale
 *
 * This file is part of the Mokkd package.
 *
 * Mokkd is free software: you can redistribute it and/or modify
 * it under the terms of the Apache License v2.0.
 *
 * Mokkd is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * Apache License for more details.
 *
 * You should have received a copy of the Apache License v2.0
 * along with Mokkd. If not, see <http://www.apache.org/licenses/>.
 */

declare(strict_types=1);

namespace Mokkd\Expectations;

use Mokkd\Contracts\Expectation as ExpectationContract;
use Mokkd\Contracts\Serialiser as SerialiserContract;

class Any extends AbstractExpectation
{
    public function matches(mixed ...$args): bool
    {
        return true;
    }

    public function isSatisfied(): bool
    {
        return $this->expectedCount === ExpectationContract::UnlimitedTimes || $this->matchCount === $this->expectedCount;
    }

    public function message(SerialiserContract $serialiser): string
    {
        return "({any arguments}) expected to be called exactly {$this->expectedCount} time(s) but called {$this->matchCount} time(s)";
    }
}
