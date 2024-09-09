<?php

declare(strict_types=1);

namespace Mokkd;

use Closure;
use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\MockFunction as MockFunctionContract;
use Mokkd\Contracts\Expectation as ExpectationContract;
use Mokkd\Expectations\AbstractExpectation;
use Mokkd\Expectations\Expectation;
use Mokkd\Expectations\ReturnMode;
use Mokkd\Matchers\Identity;

class MockFunction implements MockFunctionContract
{
    /** @var string $functionName The mocked function. */
    private string $functionName;

    private Closure $fn;

    /** @var ExpectationContract[] $expectations */
    private array $expectations = [];

    private ?AbstractExpectation $currentExpectation = null;

    public function __construct(string $functionName)
    {
        $this->functionName = strtolower($functionName);
        $mock = $this;

        $this->fn = fn(mixed ...$args) => $mock(...$args);
//        $this->fn = (function(mixed ...$args) use ($mock): mixed {
//            foreach ($mock->expectations as $expectation) {
//                if ($expectation->matches(...$args)) {
//                    return $expectation->match(...$args);
//                }
//            }
//
//            throw new ExpectationNotMatchedException();
//        })->bindTo($this);

        uopz_set_return($functionName, $this->fn, true);
    }

    public function __destruct()
    {
        $this->remove();
    }

    public function __invoke(mixed ...$args): mixed
    {
        foreach ($this->expectations as $expectation) {
            if ($expectation->matches(...$args)) {
                return $expectation->match(...$args);
            }
        }

        throw new ExpectationNotMatchedException();
    }

    private static function createMatcher(mixed $expected): MatcherContract
    {
        if ($expected instanceof MatcherContract) {
            return $expected;
        }

        return new Identity($expected);
    }

    private function checkAndCreateExpectation(): void
    {
        if (null === $this->currentExpectation) {
            $this->currentExpectation = Expectation::any();
            $this->expectations[] = $this->currentExpectation;
        }
    }

    public function expects(...$args): self
    {
        $this->currentExpectation = new Expectation(...array_map([self::class, 'createMatcher'], $args));
        $this->expectations[] = $this->currentExpectation;
        return $this;
    }

    public function once(): self
    {
        $this->checkAndCreateExpectation();
        $this->currentExpectation->setExpectedCount(1);
        return $this;
    }

    public function twice(): self
    {
        $this->checkAndCreateExpectation();
        $this->currentExpectation->setExpectedCount(1);
        return $this;
    }

    public function times(int $times): MockFunctionContract
    {
        $this->checkAndCreateExpectation();
        $this->currentExpectation->setExpectedCount($times);
        return $this;
    }

    public function never(): MockFunctionContract
    {
        $this->checkAndCreateExpectation();
        $this->currentExpectation->setExpectedCount(0);
        return $this;
    }

    public function returning(mixed $value): self
    {
        $this->checkAndCreateExpectation();
        $this->currentExpectation->setReturn($value);
        return $this;
    }

    public function returningFrom(array $values): self
    {
        $this->checkAndCreateExpectation();
        $this->currentExpectation->setReturn($values, ReturnMode::Sequential);
        return $this;
    }

    public function returningMappedValueFrom(array $values): self
    {
        $this->checkAndCreateExpectation();
        $this->currentExpectation->setReturn($values, ReturnMode::Mapped);
        return $this;
    }

    public function returningUsing(callable $fn): self
    {
        $this->checkAndCreateExpectation();
        $this->currentExpectation->setReturn($fn, ReturnMode::Callback);
        return $this;
    }

    public function remove(): void
    {
        if ($this->fn === uopz_get_return($this->functionName)) {
            uopz_unset_return($this->functionName);
        }
    }

    public function addExpectation(ExpectationContract $expectation): self
    {
        $this->expectations[] = $expectation;
        return $this;
    }
}
