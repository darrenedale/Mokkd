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

namespace MokkdTests;

use Mokkd\Contracts\Serialiser as SerialiserContract;

trait CreatesMockSerialiser
{
    /**
     * Create a "mock" Serialiser to control the serialisation of a value.
     *
     * @param mixed|array $expected The (sequence of) value(s) expected to be provided to the serialiser.
     * @param string|string[] $serialisation The (sequence of) mock serialisations to return.
     */
    private static function mockSerialiser(mixed $expected, string|array $serialisation = "The test serialisation string"): SerialiserContract
    {
        return new class($expected, $serialisation) implements SerialiserContract
        {
            private array $expected;

            private array $serialisations;

            public function __construct(mixed $expected, string|array $serialisation)
            {
                if (!is_array($expected)) {
                    $expected = [$expected];
                }

                if (!is_array($serialisation)) {
                    $serialisation = [$serialisation];
                }

                $this->expected = $expected;
                $this->serialisations = $serialisation;
            }

            public function serialise(mixed $value): string
            {
                TestCase::assertSame(array_shift($this->expected), $value);
                return array_shift($this->serialisations);
            }
        };
    }
}
