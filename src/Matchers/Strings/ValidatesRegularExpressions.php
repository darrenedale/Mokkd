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

namespace Mokkd\Matchers\Strings;

use Mokkd\Utilities\Guard;

trait ValidatesRegularExpressions
{
    protected static function isValidRegularExpression(string $pattern, string $encoding): bool
    {
        $isValid = true;

        set_error_handler(static function () use (&$isValid) {
            $isValid = false;
        });

        $previousEncoding = mb_regex_encoding();

        if ($previousEncoding !== $encoding) {
            // ensure the encoding is reset no matter how we exit this method
            $guard = new Guard(static fn() => mb_regex_encoding($previousEncoding));
            mb_regex_encoding($encoding);
        }

        // will raise a warning if the pattern is not valid
        mb_ereg($pattern, "");
        restore_error_handler();
        return $isValid;
    }
}
