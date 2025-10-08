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
 * Contract for mocked functions.
 */
interface MockFunction
{
    /** The name of the mocked function. */
    public function functionName(): string;

    /**
     * Install the mock.
     *
     * If you're using the built-in implementation you don't need to call this directly, Mokkd will do it automatically.
     */
    public function install(): void;

    /**
     * Remove the mock.
     *
     * If you're using the built-in implementation you don't need to call this directly - Mokkd will do this when your
     * test is complete and you call Mokkd::close().
     */
    public function uninstall(): void;

    /** @return Expectation[] */
    public function expectations(): array;

    /**
     * Verify all the mock's expectations.
     *
     * If any expectation is not satisfied, an ExpectationException is thrown.
     */
    public function verifyExpectations(): void;
}
