<?php

declare(strict_types=1);

namespace MokkdTests\Expectations;

use Error;
use LogicException;
use Mokkd\Expectations\Any;
use MokkdTests\CreatesNullSerialiser;
use MokkdTests\Matchers\DataFactory;
use MokkdTests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(Any::class)]
class AnyTest extends TestCase
{
    use CreatesNullSerialiser;

    private Any $any;

    public function setUp(): void
    {
        $this->any = new Any();
        $this->any->setReturn(null);
    }

    public function tearDown(): void
    {
        unset($this->any);
    }

    /** Ensure we get an error if an attempt is made to match() without setting the return. */
    public function testMatch1(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("Can't match an expectation when it doesn't have a return mode set");
        (new Any())->match();
    }

    public static function dataForTestMatches1(): iterable
    {
        yield from DataFactory::arrays();
        yield from DataFactory::strings();
        yield "no-arguments" => [];
    }

    /** Ensure (or a reasonable approximation thereof) that anything matches. */
    #[DataProvider("dataForTestMatches1")]
    public function testMatches1(mixed ...$args): void
    {
        self::assertTrue($this->any->matches(...$args));
    }

    public static function dataForTestIsSatisfied1(): iterable
    {
        yield "once" => [1, "mokkd", 42];
        yield "twice" => [2, null, true, 3.1415927];
        yield "ten-times" => [10, false, new class {}, "func", "mokkd"];
        yield "never" => [0, [1, 2, 3]];
    }

    /** Ensure isSatisfied() correctly indicates the call count is matched. */
    #[DataProvider("dataForTestIsSatisfied1")]
    public function testIsSatisfied1(int $times, mixed ...$args): void
    {
        $this->any->setExpected($times);

        while (0 < $times) {
            $this->any->match(...$args);
            --$times;
        }
        
        self::assertTrue($this->any->isSatisfied());
    }

    public static function dataForTestIsSatisfied2(): iterable
    {
        yield from self::dataForTestIsSatisfied1();
    }

    /** Ensure isSatisfied() correctly identifies that the call count has been breached. */
    #[DataProvider("dataForTestIsSatisfied2")]
    public function testIsSatisfied2(int $times, mixed ...$args): void
    {
        $this->any->setExpected($times);

        while (-1 < $times) {
            $this->any->match(...$args);
            --$times;
        }
        
        self::assertFalse($this->any->isSatisfied());
    }

    public static function dataForTestIsSatisfied3(): iterable
    {
        foreach (self::dataForTestIsSatisfied1() as $label => $args) {
            // not possible to call fewer than 0 times
            if (0 === $args[0]) {
                continue;
            }
            
            yield $label => $args;
        }
    }

    /** Ensure isSatisfied() correctly identifies the call count has not been reached. */
    #[DataProvider("dataForTestIsSatisfied3")]
    public function testIsSatisfied3(int $times, mixed ...$args): void
    {
        $this->any->setExpected($times);

        while (1 < $times) {
            $this->any->match(...$args);
            --$times;
        }
        
        self::assertFalse($this->any->isSatisfied());
    }

    public static function dataForTestMessage1(): iterable
    {
        yield "never-one" => [0, 1, "({any arguments}) expected to be called exactly 0 time(s) but called 1 time(s)"];
        yield "never-ten" => [0, 10, "({any arguments}) expected to be called exactly 0 time(s) but called 10 time(s)"];
        yield "once-never" => [1, 0, "({any arguments}) expected to be called exactly 1 time(s) but called 0 time(s)"];
        yield "once-twice" => [1, 2, "({any arguments}) expected to be called exactly 1 time(s) but called 2 time(s)"];
        yield "once-ten-times" => [1, 10, "({any arguments}) expected to be called exactly 1 time(s) but called 10 time(s)"];
        yield "twice-never" => [2, 0, "({any arguments}) expected to be called exactly 2 time(s) but called 0 time(s)"];
        yield "twice-once" => [2, 1, "({any arguments}) expected to be called exactly 2 time(s) but called 1 time(s)"];
        yield "twice-three-times" => [2, 3, "({any arguments}) expected to be called exactly 2 time(s) but called 3 time(s)"];
        yield "twice-ten-times" => [2, 10, "({any arguments}) expected to be called exactly 2 time(s) but called 10 time(s)"];
        yield "three-times-never" => [3, 0, "({any arguments}) expected to be called exactly 3 time(s) but called 0 time(s)"];
        yield "three-times-twice" => [3, 2, "({any arguments}) expected to be called exactly 3 time(s) but called 2 time(s)"];
        yield "three-times-four-times" => [3, 4, "({any arguments}) expected to be called exactly 3 time(s) but called 4 time(s)"];
        yield "three-times-ten-times" => [3, 10, "({any arguments}) expected to be called exactly 3 time(s) but called 10 time(s)"];
        yield "ten-times-never" => [10, 0, "({any arguments}) expected to be called exactly 10 time(s) but called 0 time(s)"];
        yield "ten-times-nine-times" => [10, 9, "({any arguments}) expected to be called exactly 10 time(s) but called 9 time(s)"];
        yield "ten-times-eleven-times" => [10, 11, "({any arguments}) expected to be called exactly 10 time(s) but called 11 time(s)"];
    }

    /** Ensure we get the expected error message. */
    #[DataProvider("dataForTestMessage1")]
    public function testMessage1(int $expectedTimes, int $actualTimes, string $expectedMessage): void
    {
        $this->any->setExpected($expectedTimes);
        
        while (0 < $actualTimes) {
            $this->any->match();
            --$actualTimes;
        }
        
        self::assertSame($expectedMessage, $this->any->message(self::nullSerialiser()));
    }
}
