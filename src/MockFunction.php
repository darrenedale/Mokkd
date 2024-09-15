<?php

declare(strict_types=1);

namespace Mokkd;

use Closure;
use Mokkd;
use Mokkd\Contracts\Expectation as ExpectationContract;
use Mokkd\Contracts\ExpectationBuilder as ExpectationBuilderContract;
use Mokkd\Contracts\KeyMapper as KeyMapperContract;
use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\MockFunction as MockFunctionContract;
use Mokkd\Exceptions\ExpectationException;
use Mokkd\Exceptions\FunctionException;
use Mokkd\Exceptions\UnexpectedFunctionCallException;
use Mokkd\Expectations\AbstractExpectation;
use Mokkd\Expectations\Expectation;
use Mokkd\Expectations\ReturnMode;
use Mokkd\Matchers\Comparisons\IsIdenticalTo;
use Mokkd\Utilities\Guard;
use ReflectionException;
use ReflectionFunction;
use SplFileInfo;
use function uopz_get_return;
use function uopz_set_return;
use function uopz_unset_return;

/**
 * A mock for a free-standing function.
 *
 * The mocked function can be a PHP built-in, a UDF or a function defined by a PHP extension. It must exist at the time
 * of construction. It is recommended that you obtain instances via the Core factory rather than constructing them
 * directly as this ensures that multiple mocks for the same function don't interfere with one another.
 *
 * Currently, all expectations match based on positional arguments - named arguments are not supported.
 */
class MockFunction implements MockFunctionContract, ExpectationBuilderContract
{
    /** @var string $functionName The mocked function. */
    private string $functionName;

    /** @var Closure The function with which to replace the mocked function. */
    private Closure $fn;

    /** @var ExpectationContract[] $expectations */
    private array $expectations = [];

    /** @var AbstractExpectation|null The expectation on which the builder methods are currently operating. */
    private ?AbstractExpectation $currentExpectation = null;

    /**
     * @var bool Whether the mock "blocks".
     *
     * If the mock blocks (the default), any calls without matching expectations throw. If the mock does not block, any
     * calls without matching expectations are forwarded to the original function.
     */
    private bool $blocking = true;

    /**
     * @var bool Whether the mock consumes matched expectations.
     *
     * If the mock consumes, any matched expectation that has been fully satisfied is considered consumed and won't
     * match any more calls. Combining consuming with non-blocking enables you to set a bunch of expectations which,
     * when met, cause the mock to revert back to the original function.
     */
    private bool $consuming = false;

    /** @var ReflectionFunction Enables the mock to act the same as the original function regarding its return type. */
    private ReflectionFunction $reflector;

    /**
     * Create a new mock for a given function.
     *
     * It's advised that you use the factory method Mokkd::func() instead of creating mocks directly to avoid multiple
     * mocks for the same function interfering with one another.
     *
     * The named function must exist when a mock is constructed. The named function must include the namespace if it has
     * one.
     *
     * @param string $functionName The function to mock.
     */
    public function __construct(string $functionName)
    {
        $this->functionName = $functionName;

        try {
            $this->reflector = new ReflectionFunction($functionName);
        } catch (ReflectionException $err) {
            throw new FunctionException($functionName, "Expected valid function name, found '{$functionName}'", previous: $err);
        }

        // UOPZ requires a Closure, not just an invokable
        $mock = $this;
        $this->fn = fn(mixed ...$args) => $mock(...$args);
        $this->install();
    }

    /** Ensures the mock is no longer installed. */
    public function __destruct()
    {
        $this->uninstall();
    }

    /**
     * Uopz has/had a bug in uopz_get_return() where it would fail to recognise a return set for a function with upper-
     * case characters in its name unless the provided name was all lower-case.
     *
     * This PR (https://github.com/krakjoe/uopz/pull/164) was submitted and approved but to support older versions, and
     * because I'm still seeing the behaviour in recent versions, we detect whether uopz can identify set returns
     * without having to lower-case the function name in advance.
     *
     * @return bool
     */
    private static function isUopzCaseSafe(): bool
    {
        static $safe = null;

        if (null === $safe) {
            uopz_set_return(SplFileInfo::class, "getSize", static fn(): int|false => 42);
            $safe = (null !== uopz_get_return(SplFileInfo::class, "getSize"));
            uopz_unset_return(SplFileInfo::class, "getSize");
        }

        return $safe;
    }

    /** Helper to create a matcher for an argument provided to expects(). */
    private static function createMatcher(mixed $expected): MatcherContract
    {
        return ($expected instanceof MatcherContract ? $expected : new IsIdenticalTo($expected));
    }

    /** Helper to install the mock. */
    public function install(): void
    {
        uopz_set_return($this->functionName, $this->fn, true);
    }

    /** Helper to determine whether the mocked function is void. */
    protected function isVoid(): bool
    {
        return "void" === (string) $this->reflector->getReturnType();
    }

    /** @return mixed|void */
    protected function invokeOriginal(mixed ...$args)
    {
        // always reinstall the mock no matter how we exit this method
        $guard = new Guard(fn() => $this->install());
        $this->uninstall();

        if ($this->isVoid()) {
            ($this->functionName)(...$args);
            return;
        }

        return ($this->functionName)(...$args);
    }

    /**
     * Access the current expectation that the builder methods are working with.
     *
     * If this is called before expects(), an expectation that matches any arguments is automatically generated.
     */
    protected function currentExpectation(): ExpectationContract
    {
        if (null === $this->currentExpectation) {
            $this->currentExpectation = Expectation::any();
            $this->expectations[] = $this->currentExpectation;
        }

        return $this->currentExpectation;
    }

    /** @return mixed|void */
    public function __invoke(mixed ...$args)
    {
        foreach ($this->expectations as $expectation) {
            if ($expectation->matches(...$args) && (!$this->consuming || !$expectation->isSatisfied())) {
                if ($this->isVoid()) {
                    $expectation->match(...$args);
                    return;
                } else {
                    return $expectation->match(...$args);
                }
            }
        }

        if (!$this->blocking) {
            if ($this->isVoid()) {
                $this->invokeOriginal(...$args);
                return;
            } else {
                return $this->invokeOriginal(...$args);
            }
        }

        throw new UnexpectedFunctionCallException(
            $this->functionName(),
            "No matching expectation found for function call {$this->functionName}("
            . implode(", ", array_map([Mokkd::serialiser(), "serialise"], $args)) . ")"
        );
    }

    /** The name of the mocked function, including its namespace (if any). */
    public function functionName(): string
    {
        return $this->functionName;
    }

    /**
     * Start building a new expectation.
     *
     * The provided arguments will be used to match calls to the mocked function. Matching is based on identity for
     * direct values, which notably means match arguments that are objects must be the same instance for a match to
     * occur. If any argument provided is a Matcher instance, the argument in that position will be provided to the
     * Matcher's matches() method, and if it returns true that argument is considered a match. The consequence of this
     * is that functions that accept Matcher instances as arguments are difficult to mock effectively. It's a niche
     * use-case, but if you really need to do it you can use a Callback Matcher with a callback that checkes the
     * argument is a Matcher with the correct type and state.
     *
     * @param mixed ...$args The arguments the expectation will match against.
     */
    public function expects(mixed ...$args): self
    {
        $this->currentExpectation = new Expectation(...array_map([self::class, 'createMatcher'], $args));
        $this->expectations[] = $this->currentExpectation;
        return $this;
    }

    /** Set the current expectation to expect to match exactly once. */
    public function once(): self
    {
        return $this->times(1);
    }

    /** Set the current expectation to expect to match exactly twice. */
    public function twice(): self
    {
        return $this->times(2);
    }

    /**
     * Set the current expectation to expect to match a precise number of times.
     *
     * @param int $times The number of expected matches. Must be >= 0.
     */
    public function times(int $times): self
    {
        $this->currentExpectation()->setExpected($times);
        return $this;
    }

    /** Set the current expectation to expect never to be matched. */
    public function never(): self
    {
        return $this->times(0);
    }

    /**
     * Set the return value of the current expectation.
     *
     * The current expectation will return the provided value when matched.
     */
    public function returning(mixed $value): self
    {
        $this->currentExpectation()->setReturn($value);
        return $this;
    }

    /**
     * Set the current expectation to return sequential items from an array.
     *
     * The current expectation will return the first item from the array the first time it matches, the second item on
     * the second match, and so on. When the array is exhausted, it wraps back to the first item.
     */
    public function returningFrom(array $values): self
    {
        $this->currentExpectation()->setReturn($values, ReturnMode::Sequential);
        return $this;
    }

    /**
     * Set the current expectation to return items from a map.
     *
     * The current expectation will return an item from the map using a key determined by examining the call arguments.
     * How the key is selected is determined by the provided Mapper.
     *
     * @param array<string,mixed> $map The map of return values.
     * @param int|KeyMapperContract $keyMapper The mapper that turns the call arguments into a key for the lookup in the map.
     * If an int is provided, it's treated as the index of the argument to use as the map key.
     */
    public function returningMappedValueFrom(array $map, int|KeyMapperContract $keyMapper): self
    {
        $this->currentExpectation()->setReturn($map, ReturnMode::Mapped);
        return $this;
    }

    /**
     * Set the current expectation to return the result of calling a callback.
     *
     * The current expectation will return the result of invoking a callback with the mocked function's arguments.
     *
     * @api
     */
    public function returningUsing(callable $fn): self
    {
        $this->currentExpectation()->setReturn($fn, ReturnMode::Callback);
        return $this;
    }

    /**
     * Set the mock to block unmatched function calls.
     *
     * If no expectation can be found matching a call, throw an UnexpectedFunctionCallException.
     *
     * @api
     */
    public function blocking(): self
    {
        $this->blocking = true;
        return $this;
    }

    /** Set the mock to pass through unmatched calls to the original function. */
    public function withoutBlocking(): self
    {
        $this->blocking = false;
        return $this;
    }

    /**
     * Set the mock to consume expectations as they are matched.
     *
     * When consuming, each expectation is considered consumed when satisfied and won't match again.
     *
     * @api
     */
    public function consuming(): self
    {
        $this->consuming = true;
        return $this;
    }

    /** Set the mock not to consume expectations as they are matched. */
    public function withoutConsuming(): self
    {
        $this->consuming = false;
        return $this;
    }

    /** Uninstall the mock, restoring the original function. */
    public function uninstall(): void
    {
        $uopzFunctionName = $this->functionName;

        if (!self::isUopzCaseSafe()) {
            // ensure we don't interfere with any mocks set on strtolower
            $strtolower = uopz_get_return("strtolower");

            if (null !== $strtolower) {
                uopz_unset_return("strtolower");
            }
        }

        // PR for case-sensitivity bug in uopz_get_return() doesn't appear to be in released extension
        if ($this->fn === uopz_get_return($uopzFunctionName)) {
            uopz_unset_return($this->functionName);
        }

        if (null !== $strtolower) {
            uopz_set_return("strtolower", $strtolower);
        }
    }

    /**
     * Add an expectation to the mock function.
     *
     * Generally it's advised to use the expectation building methods to compose expectations, but you can add arbitrary
     * expectations using this method if you need to.
     *
     * @api
     */
    public function addExpectation(ExpectationContract $expectation): self
    {
        $this->expectations[] = $expectation;
        return $this;
    }

    /**
     * Fetch all the mock function's expectations.
     *
     * @api
     */
    public function expectations(): array
    {
        return $this->expectations;
    }

    /** Verify that all the mock function's expectations have been satisfied. */
    public function verifyExpectations(): void
    {
        foreach ($this->expectations() as $expectation) {
            if (!$expectation->isSatisfied()) {
                throw new ExpectationException($expectation, "{$this->functionName()}{$expectation->message()}");
            }
        }
    }
}
