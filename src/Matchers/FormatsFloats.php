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

namespace Mokkd\Matchers;

use function str_contains;

trait FormatsFloats
{
    protected static function formatFloat(float $number): string
    {
        $formatted = (string) $number;

        if (!str_contains($formatted, '.')) {
            return $formatted . ".0";
        }

        return $formatted;
    }
}
