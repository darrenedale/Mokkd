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

use InvalidArgumentException;
use LogicException;
use Mokkd\Contracts\Expectation as ExpectationContract;
use Mokkd\Contracts\ExpectationReturn as ExpectationReturnContract;
use Mokkd\Contracts\KeyMapper as KeyMapperContract;
use Mokkd\Exceptions\ExpectationException;
use Mokkd;
use Mokkd\Utilities\ExpectationReturn;
use Throwable;

abstract class AbstractExpectation implements ExpectationContract
{
    private ReturnMode $returnMode;

    private ?KeyMapperContract $mapper = null;

    private mixed $returnValue;

    protected int $matchCount = 0;

    protected int $expectedCount = self::UnlimitedTimes;

    /** Helper to check and fetch the mapped return key. */
    private function returnFromMap(mixed ...$args): mixed
    {
        $key = $this->mapper->mapKey(...$args);

        if (!array_key_exists($key, $this->returnValue)) {
            throw new LogicException("Expected mapped key, found \"{$key}\"");
        }

        return $this->returnValue[$key];
    }

    public function match(...$args): ExpectationReturnContract
    {
        assert(isset($this->returnMode), new LogicException("Can't match an expectation when it doesn't have a return mode set"));

        if (!$this->matches(...$args)) {
            throw new ExpectationException(
                $this,
                sprintf(
                    "Expectation does not match arguments (%s)",
                    implode(", ", array_map([Mokkd::serialiser(), "serialise"], $args))
                )
            );
        }

        ++$this->matchCount;

        return match($this->returnMode) {
            ReturnMode::Void => ExpectationReturn::void(),
            ReturnMode::Value => ExpectationReturn::create($this->returnValue),
            ReturnMode::Callback => ExpectationReturn::create(($this->returnValue)(...$args)),
            ReturnMode::Sequential => ExpectationReturn::create($this->returnValue[($this->matchCount - 1) % count($this->returnValue)]),
            ReturnMode::Mapped => ExpectationReturn::create($this->returnFromMap(...$args)),
            ReturnMode::Throw => throw $this->returnValue,
        };
    }

    public function matched(): int
    {
        return $this->matchCount;
    }

    public function setExpected(int $count): void
    {
        assert(self::UnlimitedTimes === $count || 0 <= $count, new LogicException("Expected \$count >= 0 or == ExpectationContract::UnlimitedTimes, found {$count}"));
        $this->expectedCount = $count;
    }

    public function expected(): int
    {
        return $this->expectedCount;
    }

    /** Helper to set return-by-value. */
    protected function setReturnValue(mixed $value): void
    {
        $this->returnValue = $value;
        $this->returnMode = ReturnMode::Value;
    }

    /** Helper to set return-by-callback. */
    protected function setReturnCallback(mixed $callback): void
    {
        assert(is_callable($callback), new LogicException("Expecting valid callable"));
        $this->returnValue = $callback;
        $this->returnMode = ReturnMode::Callback;
    }

    protected function setReturnArray(mixed $array): void
    {
        assert(is_array($array) && 0 < count($array) && array_is_list($array), new LogicException("Expecting valid array"));
        $this->returnValue = $array;
        $this->returnMode = ReturnMode::Sequential;
    }

    protected function setReturnMap(mixed $map, KeyMapperContract $mapper): void
    {
        assert(is_array($map) && 0 < count($map), new LogicException("Expecting valid map"));
        $this->returnValue = $map;
        $this->mapper = $mapper;
        $this->returnMode = ReturnMode::Mapped;
    }

    /** Set what the mocked function should return when this expectation is matched. */
    public function setReturn(mixed $returnValue, ReturnMode $returnMode = ReturnMode::Value, ?KeyMapperContract $mapper = null): void
    {
        assert(ReturnMode::Mapped !== $returnMode || $mapper instanceof KeyMapperContract, new LogicException("A Mapper must be provided when the return mode is Mapped"));

        match ($returnMode) {
            ReturnMode::Void => $this->setVoid(),
            ReturnMode::Value => $this->setReturnValue($returnValue),
            ReturnMode::Callback => $this->setReturnCallback($returnValue),
            ReturnMode::Sequential => $this->setReturnArray($returnValue),
            ReturnMode::Mapped => $this->setReturnMap($returnValue, $mapper),
            ReturnMode::Throw => throw new InvalidArgumentException("Use setThrow() to set an expectation to throw")
        };

    }

    /** Set what the mocked function should throw when this expectation is matched. */
    public function setThrow(Throwable $error): void
    {
        $this->returnMode = ReturnMode::Throw;
        $this->returnValue = $error;
    }

    /** Set the mocked function to return void when this expectation is matched. */
    public function setVoid(): void
    {
        $this->returnMode = ReturnMode::Void;
        $this->returnValue = null;
    }
}
