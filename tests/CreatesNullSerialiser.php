<?php

declare(strict_types=1);

namespace MokkdTests;

use Mokkd\Contracts\Serialiser as SerialiserContract;

trait CreatesNullSerialiser
{
    private static function nullSerialiser(): SerialiserContract
    {
        return new class implements SerialiserContract
        {
            public function serialise(mixed $value): string
            {
                return "";
            }
        };
    }
}
