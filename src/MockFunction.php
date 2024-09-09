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
use Mokkd\Utilities\Guard;
use ReflectionException;
use ReflectionFunction;
use RuntimeException;

use function uopz_get_return;
use function uopz_set_return;
use function uopz_unset_return;

class MockFunction implements MockFunctionContract
{
    /** @var string $functionName The mocked function. */
    private string $functionName;

    private Closure $fn;

    /** @var ExpectationContract[] $expectations */
    private array $expectations = [];

    private ?AbstractExpectation $currentExpectation = null;

    private bool $blocking = true;

    private bool $consuming = false;

    private ReflectionFunction $reflector;

    public function __construct(string $functionName)
    {
        $this->functionName = strtolower($functionName);

        try {
            $this->reflector = new ReflectionFunction($this->functionName);
        } catch (ReflectionException $err) {
            // TODO use our own exception class
            throw new RuntimeException("Expected valid function name, found '{$functionName}'", previous: $err);
        }

        // UOPZ requires a Closure, not just an invocable
        $mock = $this;
        $this->fn = fn(mixed ...$args) => $mock(...$args);
        $this->install();
    }

    public function __destruct()
    {
        $this->uninstall();
    }

    /** @return mixed|void */
    public function __invoke(mixed ...$args)
    {
        foreach ($this->expectations as $expectation) {
            if (
                $expectation->matches(...$args)
                && (!$this->consuming || $expectation->matched() < $expectation->expected())
            ) {
                return $expectation->match(...$args);
            }
        }

        if (!$this->blocking) {
            return $this->callOriginal(...$args);
        }

        throw new ExpectationNotMatchedException("No matching expectation found for function call {$this->functionName}(" . implode(", ", Core::serialiser()->serialise(...$args)) . ")");
    }

    private static function createMatcher(mixed $expected): MatcherContract
    {
        return ($expected instanceof MatcherContract ? $expected : new Identity($expected));
    }

    private function checkAndCreateExpectation(): void
    {
        if (null === $this->currentExpectation) {
            $this->currentExpectation = Expectation::any();
            $this->expectations[] = $this->currentExpectation;
        }
    }

    /** @return mixed|void */
    private function callOriginal(mixed ...$args)
    {
        // always reinstall the mock no matter how we exit this method
        $guard = new Guard(fn() => $this->install());
        $this->uninstall();

        if (!$this->reflector->hasReturnType() || "void" !== (string) $this->reflector->getReturnType()) {
            return ($this->functionName)(...$args);
        }

        ($this->functionName)(...$args);
    }

    public function name(): string
    {
        return $this->functionName;
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
        $this->currentExpectation->setExpected(1);
        return $this;
    }

    public function twice(): self
    {
        $this->checkAndCreateExpectation();
        $this->currentExpectation->setExpected(2);
        return $this;
    }

    public function times(int $times): MockFunctionContract
    {
        $this->checkAndCreateExpectation();
        $this->currentExpectation->setExpected($times);
        return $this;
    }

    public function never(): MockFunctionContract
    {
        $this->checkAndCreateExpectation();
        $this->currentExpectation->setExpected(0);
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

    public function withoutBlocking(): self
    {
        $this->blocking = false;
        return $this;
    }

    public function consuming(): self
    {
        $this->consuming = true;
        return $this;
    }

    public function uninstall(): void
    {
        if ($this->fn === uopz_get_return($this->functionName)) {
            uopz_unset_return($this->functionName);
        }
    }

    protected function install(): void
    {
        uopz_set_return($this->functionName, $this->fn, true);
    }

    public function addExpectation(ExpectationContract $expectation): self
    {
        $this->expectations[] = $expectation;
        return $this;
    }

    public function expectations(): array
    {
        return $this->expectations;
    }
}
