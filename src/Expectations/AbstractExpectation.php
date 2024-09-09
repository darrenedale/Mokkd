<?php

declare(strict_types=1);

namespace Mokkd\Expectations;

use Mokkd\Contracts\Expectation;
use Mokkd\ExpectationNotMatchedException;
use LogicException;

abstract class AbstractExpectation implements Expectation
{
    public const UnlimitedTimes = -1;

    private ReturnMode $returnMode;

    private mixed $returnValue;

    private int $matchCount = 0;

    private int $expectedCount = self::UnlimitedTimes;

    protected function mappedReturnKeyForArguments(mixed ...$args): string|int
    {
        return "";
    }

    public function match(...$args): mixed
    {
        if (!$this->matches(...$args)) {
            throw new ExpectationNotMatchedException();
        }

        ++$this->matchCount;

        return match($this->returnMode) {
            ReturnMode::Value => $this->returnValue,
            ReturnMode::Callback => ($this->returnValue)(...$args),
            ReturnMode::Sequential => $this->returnValue[($this->matchCount - 1) % count($this->returnValue)],
            ReturnMode::Mapped => $this->returnValue[$this->mappedReturnKeyForArguments(...$args)],
        };
    }

    public function matched(): int
    {
        return $this->matchCount;
    }

    public function setExpectedCount(int $count): void
    {
        assert(self::UnlimitedTimes === $count || 0 <= $count, new LogicException("Expected \$count >= 0 or == AbstractExpectation::UnlimitedTimes, found {$count}"));
        $this->expectedCount = $count;
    }

    public function expectedCount(): int
    {
        return $this->expectedCount;
    }

    protected function setReturnValue(mixed $value): void
    {
        $this->returnValue = $value;
        $this->returnMode = ReturnMode::Value;
    }

    protected function setReturnCallback(mixed $callback): void
    {
        assert(is_callable($callback, true), new LogicException("Expecting valid callable"));
        $this->returnValue = $callback;
        $this->returnMode = ReturnMode::Callback;
    }

    protected function setReturnArray(mixed $array): void
    {
        assert(is_array($array) && array_is_list($array), new LogicException("Expecting valid array"));
        $this->returnValue = $array;
        $this->returnMode = ReturnMode::Sequential;
    }

    protected function setReturnMap(mixed $map): void
    {
        assert(is_array($map), new LogicException("Expecting valid map"));
        $this->returnValue = $map;
        $this->returnMode = ReturnMode::Mapped;
    }

    public function setReturn(mixed $returnValue, ReturnMode $returnMode = ReturnMode::Value): void
    {
        match ($returnMode) {
            ReturnMode::Value => $this->setReturnValue($returnValue),
            ReturnMode::Callback => $this->setReturnCallback($returnValue),
            ReturnMode::Sequential => $this->setReturnArray($returnValue),
            ReturnMode::Mapped => $this->setReturnMap($returnValue),
        };
    }
}
