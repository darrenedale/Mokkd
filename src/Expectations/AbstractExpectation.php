<?php

declare(strict_types=1);

namespace Mokkd\Expectations;

use LogicException;
use Mokkd\Contracts\Expectation as ExpectationContract;
use Mokkd\Contracts\KeyMapper as KeyMapperContract;
use Mokkd\Exceptions\ExpectationException;
use Mokkd;

abstract class AbstractExpectation implements ExpectationContract
{
    private ReturnMode $returnMode;

    private ?KeyMapperContract $mapper = null;

    private mixed $returnValue;

    protected int $matchCount = 0;

    protected int $expectedCount = self::UnlimitedTimes;

    public function match(...$args): mixed
    {
        if (!$this->matches(...$args)) {
            throw new ExpectationException($this, sprintf("Expectation does not match arguments (%s)", implode(", ", Mokkd::serialiser()->serialise(...$args))));
        }

        ++$this->matchCount;

        return match($this->returnMode) {
            ReturnMode::Value => $this->returnValue,
            ReturnMode::Callback => ($this->returnValue)(...$args),
            ReturnMode::Sequential => $this->returnValue[($this->matchCount - 1) % count($this->returnValue)],
            ReturnMode::Mapped => $this->returnValue[$this->mapper->mapKey(...$args)],
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

    protected function setReturnMap(mixed $map, KeyMapperContract $mapper): void
    {
        assert(is_array($map), new LogicException("Expecting valid map"));
        $this->returnValue = $map;
        $this->returnMode = ReturnMode::Mapped;
    }

    public function setReturn(mixed $returnValue, ReturnMode $returnMode = ReturnMode::Value, ?KeyMapperContract $mapper = null): void
    {
        assert(ReturnMode::Mapped !== $returnMode || $mapper instanceof KeyMapperContract, new LogicException("A Mapper must be provided when the return mode is Mapped"));

        match ($returnMode) {
            ReturnMode::Value => $this->setReturnValue($returnValue),
            ReturnMode::Callback => $this->setReturnCallback($returnValue),
            ReturnMode::Sequential => $this->setReturnArray($returnValue),
            ReturnMode::Mapped => $this->setReturnMap($returnValue, $mapper),
        };
    }
}
