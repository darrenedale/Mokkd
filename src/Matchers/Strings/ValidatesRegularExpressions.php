<?php

namespace Mokkd\Matchers\Strings;

trait ValidatesRegularExpressions
{
    protected static function isValidRegularExpression($pattern): bool
    {
        $isValid = true;

        $handler = set_error_handler(static function () use (&$isValid) {
            $isValid = false;
        });

        // will raise a warning if the pattern is not valid
        mb_ereg($pattern, "");
        set_error_handler($handler);
        return $isValid;
    }
}
