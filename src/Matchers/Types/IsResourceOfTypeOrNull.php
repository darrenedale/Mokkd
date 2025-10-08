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

namespace Mokkd\Matchers\Types;

use Mokkd\Contracts\Serialiser as SerialiserContract;
use Mokkd\Matchers\Composite\MatchesAnyOf;

/**
 * Matcher that requires the test value to be an open resource of a specified type or null.
 *
 * Closed resources lose their type information, so it's not possible to match a closed resource of a specified type.
 */
class IsResourceOfTypeOrNull extends MatchesAnyOf
{
    private string $type;

    public function __construct(string $type)
    {
        parent::__construct(new IsNull(), new IsResourceOfType($type));
        $this->type = $type;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function describe(SerialiserContract $serialiser): string
    {
        return "(?resource) {{$this->type()}}";
    }
}
