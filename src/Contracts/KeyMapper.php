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

namespace Mokkd\Contracts;

/**
 * Contract for classes that turn call arguments into keys for expectations that return using maps.
 *
 * Function mocks can be set up to return a value mapped from an associative array, based on the arguments provided
 * in the mocked function call. An instance of this interface will take the arguments and turn them into a key to use
 * to retrieve the return value from the associative array.
 */
interface KeyMapper
{
    /** Determine the key to use for a given set of arguments. */
    public function mapKey(mixed ...$args): string|int;
}
