<?php

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
